<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Application\Commands\Calendar;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Services\Bookable\BookableApplicationService;
use AmeliaBooking\Domain\Services\Entity\EntityService;
use AmeliaBooking\Domain\Services\TimeSlot\TimeSlotService;
use AmeliaBooking\Application\Services\TimeSlot\TimeSlotService as ApplicationTimeSlotService;
use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Entity\Bookable\Service\Service;
use AmeliaBooking\Domain\Entity\Booking\SlotsEntities;
use AmeliaBooking\Domain\Services\DateTime\DateTimeService;
use AmeliaBooking\Domain\Services\Settings\SettingsService;
use Exception;

class GetCalendarSlotEntitiesCommandHandler extends CommandHandler
{
    public function handle(GetCalendarSlotEntitiesCommand $command)
    {
        $result = new CommandResult();

        $params = $command->getFields();

        /** @var EntityService $entityService */
        $entityService = $this->container->get('domain.entity.service');
        /** @var TimeSlotService $timeSlotService */
        $timeSlotService = $this->container->get('domain.timeSlot.service');
        /** @var ApplicationTimeSlotService $applicationTimeSlotService */
        $applicationTimeSlotService = $this->container->get('application.timeSlot.service');

        $searchStartDateString = $params['date'];
        $searchEndDateString   = $params['date'];
        $searchTime            = $params['time'] ?? null;

        $slotsEntities = $applicationTimeSlotService->getSlotsEntities(
            [
                'isFrontEndBooking' => false,
                'providerIds'       => !empty($params['providers']) ? $params['providers'] : [],
            ]
        );

        $startDateTimeObject = DateTimeService::getCustomDateTimeObject($searchStartDateString . ' 00:00:00');
        $endDateTimeObject   = DateTimeService::getCustomDateTimeObject($searchEndDateString . ' 23:59:00');

        $props = [
            'providerIds'          => !empty($params['providers']) ? $params['providers'] : [],
            'locationId'           => !empty($params['location']) ? (int)$params['location'] : null,
            'extras'               => [],
            'excludeAppointmentId' => null,
            'personsCount'         => 1,
            'isFrontEndBooking'    => false,
            'startDateTime'        => $startDateTimeObject->modify('-1 days'),
            'endDateTime'          => $endDateTimeObject->modify('+1 days'),
        ];

        $settings = $applicationTimeSlotService->getSlotsSettings(false, $slotsEntities);

        $applicationTimeSlotService->setBlockerAppointments($slotsEntities->getProviders(), $props);

        $appointments = $applicationTimeSlotService->getBookedAppointments($slotsEntities, $props);
        $servicesIds  = !empty($params['services']) ? $params['services'] : $slotsEntities->getServices()->keys();

        $resultServicesIds  = [];
        $resultProvidersIds = [];
        $resultLocationIds  = [];
        foreach ($servicesIds as $serviceId) {
            $filteredSlotEntities = $entityService->getFilteredSlotsEntities(
                $settings,
                array_merge($props, ['serviceId' => $serviceId]),
                $slotsEntities
            );

            /** @var Service $service */
            $service = $filteredSlotEntities->getServices()->getItem($serviceId);

            if (!$service->getShow()->getValue()) {
                continue;
            }

            $startDateTime = DateTimeService::getCustomDateTimeObject($searchStartDateString);

            $endDateTime = DateTimeService::getCustomDateTimeObject($searchEndDateString);

            $freeSlots = $timeSlotService->getSlots(
                $settings,
                array_merge(
                    $props,
                    [
                        'serviceId'     => $serviceId,
                        'startDateTime' => $startDateTime,
                        'endDateTime'   => $endDateTime->modify('+1 day'),
                    ]
                ),
                $filteredSlotEntities,
                $appointments
            )['available'];

            if ($searchTime && array_key_exists($searchStartDateString, $freeSlots)) {
                $freeSlots = $this->filterByTime($searchStartDateString, $searchTime, $freeSlots);
            }

            if (!array_key_exists($searchStartDateString, $freeSlots)) {
                continue;
            }

            foreach ($freeSlots as $dateSlot) {
                foreach ($dateSlot as $timeSlot) {
                    foreach ($timeSlot as $infoSlot) {
                        $resultProvidersIds[] = $infoSlot[0];
                        $resultLocationIds[]  = $infoSlot[1];
                    }
                }
            }

            $resultServicesIds[] = $serviceId;
        }

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully retrieved searched services.');
        $result->setData(
            [
            'services'  => $resultServicesIds,
            'employees' => array_values(array_unique($resultProvidersIds)),
            'locations' => array_values(array_unique($resultLocationIds)),
            ]
        );

        return $result;
    }

    /**
     * @param $date
     * @param $time
     * @param $freeSlots
     *
     * @return mixed
     *
     * @throws Exception
     */
    private function filterByTime($date, $time, $freeSlots)
    {
        foreach (array_keys($freeSlots[$date]) as $freeSlotKey) {
            if (
                DateTimeService::getCustomDateTimeObject($date . ' ' . $freeSlotKey) >=
                DateTimeService::getCustomDateTimeObject($date . ' ' . $time)
            ) {
                break;
            }

            unset($freeSlots[$date][$freeSlotKey]);

            if (empty($freeSlots[$date])) {
                unset($freeSlots[$date]);
            }
        }

        return $freeSlots;
    }
}
