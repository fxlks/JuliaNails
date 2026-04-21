<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Application\Commands\Calendar;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Booking\EventApplicationService;
use AmeliaBooking\Application\Services\User\ProviderApplicationService;
use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Booking\Appointment\Appointment;
use AmeliaBooking\Domain\Entity\Booking\Event\Event;
use AmeliaBooking\Domain\Entity\Booking\Event\EventPeriod;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Entity\Schedule\BlockTime;
use AmeliaBooking\Domain\Entity\User\AbstractUser;
use AmeliaBooking\Domain\Services\DateTime\DateTimeService;
use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Domain\ValueObjects\String\BookingStatus;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Booking\Appointment\AppointmentRepository;
use AmeliaBooking\Infrastructure\WP\Translations\BackendStrings;
use AmeliaVendor\Psr\Container\ContainerExceptionInterface;
use DateInvalidTimeZoneException;
use DateTimeZone;

class GetCalendarEventsCommandHandler extends CommandHandler
{
    /**
     * @param GetCalendarEventsCommand $command
     * @return CommandResult
     * @throws AccessDeniedException
     * @throws QueryExecutionException
     */
    public function handle(GetCalendarEventsCommand $command): CommandResult
    {
        $result      = new CommandResult();
        $queryParams = $command->getField('queryParams');

        if (
            !$command->getPermissionService()->currentUserCanRead(Entities::APPOINTMENTS) &&
            !$command->getPermissionService()->currentUserCanRead(Entities::EVENTS)
        ) {
            throw new AccessDeniedException('You are not allowed to read calendar events.');
        }

        /** @var AbstractUser $user */
        $user = $this->container->get('logged.in.user');
        $timeZone = DateTimeService::getTimeZone()->getName();
        $userType = $user->getType();

        $timeZone = '';

        if ($user->getType() === Entities::CUSTOMER) {
            if (!$user->getId()) {
                throw new AccessDeniedException('You are not allowed to read calendar events.');
            }

            $queryParams['customers'] = [$user->getId()->getValue()];
        }

        if ($userType === Entities::PROVIDER) {
            $queryParams['providers'] = [$user->getId()->getValue()];
            /** @var ProviderApplicationService $providerAS */
            $providerAS = $this->container->get('application.user.provider.service');
            $timeZone = $providerAS->getTimeZone($user);
        }

        $sortedItems = array_merge(
            $this->getAppointments($queryParams, $timeZone),
            $this->getEvents($queryParams, $timeZone),
            $this->getBlockTimes($queryParams, $timeZone)
        );

        usort($sortedItems, function ($a, $b) {
            $startA = $a instanceof Appointment ? $a->getBookingStart()->getValue()
                : ($a instanceof BlockTime ? $a->getStartDate()->getValue() : $a['eventPeriod']->getPeriodStart()->getValue());
            $startB = $b instanceof Appointment ? $b->getBookingStart()->getValue()
                : ($b instanceof BlockTime ? $b->getStartDate()->getValue() : $b['eventPeriod']->getPeriodStart()->getValue());
            return $startA <=> $startB;
        });

        $maxNumberOfEvents = $queryParams['view'] === 'dayGridMonth' ? 4
            : (in_array($queryParams['view'], ['dayGridMonthSevenDays', 'dayGridMonthMobile']) ? 2 : PHP_INT_MAX);

        $filledDays = [];

        foreach ($sortedItems as $item) {
            $isAppointment = $item instanceof Appointment;
            $isBlockTime = $item instanceof BlockTime;

            $itemStartDate = $isAppointment ? $item->getBookingStart()->getValue()->format('Y-m-d')
                : ($isBlockTime ? $item->getStartDate()->getValue()->format('Y-m-d')
                    : $item['eventPeriod']->getPeriodStart()->getValue()->format('Y-m-d'));

            if (!isset($filledDays[$itemStartDate])) {
                $filledDays[$itemStartDate] = ['events' => [], 'count' => 0, 'more' => 0];
            }

            if ($filledDays[$itemStartDate]['count'] >= $maxNumberOfEvents) {
                $filledDays[$itemStartDate]['more']++;
                $this->processEventDates($filledDays, $item, 'more');
                continue;
            }

            if ($isAppointment) {
                $filledDays[$itemStartDate]['events'][] = $this->appointmentFormatter($item, $user);
            } elseif ($isBlockTime) {
                $filledDays[$itemStartDate]['events'][] = $this->blockTimeFormatter($item);
            } else {
                $filledDays[$itemStartDate]['events'][] = $this->eventFormatter($item['event'], $item['eventPeriod'], $queryParams);
            }

            $filledDays[$itemStartDate]['count']++;
            $this->processEventDates($filledDays, $item, 'count');
        }


        $result->setData(['events' => $filledDays]);

        return $result;
    }

    /**
     * @param array $filledDays
     * @param array|Appointment|BlockTime $item
     * @param string $counterKey
     * @return void
     */
    private function processEventDates(array &$filledDays, $item, string $counterKey): void
    {
        if (!$item instanceof Appointment && !$item instanceof BlockTime) {
            $eventStartDate = $item['eventPeriod']->getPeriodStart()->getValue()->setTime(0, 0, 0);
            $eventEndDate   = $item['eventPeriod']->getPeriodEnd()->getValue()->setTime(23, 59, 59);

            for ($date = (clone $eventStartDate)->modify('+1 day'); $date <= $eventEndDate; $date->modify('+1 day')) {
                $formattedDate = $date->format('Y-m-d');
                if (!isset($filledDays[$formattedDate])) {
                    $filledDays[$formattedDate] = ['events' => [], 'count' => 0, 'more' => 0];
                }
                $filledDays[$formattedDate][$counterKey]++;
            }
        }
    }

    /**
     * @throws QueryExecutionException
     * @throws DateInvalidTimeZoneException
     */
    private function getAppointments(array $queryParams, string $timeZone): array
    {
        if (!isset($queryParams['entitiesToShow']) || !in_array('appointments', $queryParams['entitiesToShow']) || !empty($queryParams['events'])) {
            return [];
        }

        /** @var AppointmentRepository $appointmentRepository */
        $appointmentRepository = $this->container->get('domain.booking.appointment.repository');

        $queryParams['statuses'] =
            isset($queryParams['statuses']) && in_array('pendingAppointments', $queryParams['statuses'])
            ? [BookingStatus::APPROVED, BookingStatus::PENDING]
            : [BookingStatus::APPROVED];

        $queryParams['dates'] = [$queryParams['calendarStartDate'], $queryParams['calendarEndDate']];
        $queryParams['withLocations'] = true;

        $appointments = $appointmentRepository->getFiltered($queryParams);

        if ($timeZone) {
            /** @var Appointment $appointment */
            foreach ($appointments->getItems() as $appointment) {
                $appointment->getBookingStart()->getValue()->setTimezone(new DateTimeZone($timeZone));

                $appointment->getBookingEnd()->getValue()->setTimezone(new DateTimeZone($timeZone));
            }
        }

        return $appointments->getItems();
    }

    /**
     * @throws DateInvalidTimeZoneException
     * @throws InvalidArgumentException
     * @throws ContainerExceptionInterface
     * @throws QueryExecutionException
     */
    private function getEvents(array $queryParams, string $timeZone): array
    {
        if (!isset($queryParams['entitiesToShow']) || !in_array('events', $queryParams['entitiesToShow']) || !empty($queryParams['services'])) {
            return [];
        }

        $eventPeriods = [];

        /** @var EventApplicationService $eventAS */
        $eventAS = $this->container->get('application.booking.event.service');

        $queryParams['dates'] = [$queryParams['calendarStartDate'], $queryParams['calendarEndDate']];

        if (!empty($queryParams['events'])) {
            $queryParams['id'] = $queryParams['events'];
        }

        $events = $eventAS->getEventsByCriteria(
            $queryParams,
            ['fetchEventsPeriods' => true, 'fetchEventsLocation' => true, 'fetchEventsOrganizer' => true],
            -1
        );

        if ($timeZone) {
            /** @var Event $event */
            foreach ($events->getItems() as $event) {
                /** @var EventPeriod $period */
                foreach ($event->getPeriods()->getItems() as $period) {
                    $period->getPeriodStart()->getValue()->setTimezone(new DateTimeZone($timeZone));

                    $period->getPeriodEnd()->getValue()->setTimezone(new DateTimeZone($timeZone));
                }
            }
        }

        /** @var Event $event */
        foreach ($events->getItems() as $event) {
            /** @var EventPeriod $eventPeriod */
            foreach ($event->getPeriods()->getItems() as $eventPeriod) {
                $eventPeriods[] = ['event' => $event, 'eventPeriod' => $eventPeriod];
            }
        }

        return $eventPeriods;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws DateInvalidTimeZoneException
     */
    private function getBlockTimes(array $queryParams, string $timeZone): array
    {
        $dayOffRepository = $this->container->get('domain.schedule.dayOff.repository');

        $queryParams['type'] = 'blockTime';
        $queryParams['dates'] = [$queryParams['calendarStartDate'], $queryParams['calendarEndDate']];

        $blockTimes = $dayOffRepository->getFiltered($queryParams);

        if ($timeZone) {
            /** @var BlockTime $blockTime */
            foreach ($blockTimes->getItems() as $blockTime) {
                $blockTime->getStartDate()->getValue()->setTimezone(new DateTimeZone($timeZone));

                $blockTime->getEndDate()->getValue()->setTimezone(new DateTimeZone($timeZone));
            }
        }

        return $blockTimes->getItems();
    }

    private function appointmentFormatter(Appointment $appointment, AbstractUser $user): array
    {
        /** @var SettingsService $settingsService */
        $settingsService = $this->container->get('domain.settings.service');
        $timeSlotStep    = $settingsService->getSetting('general', 'timeSlotLength');
        $appointmentDurationInSeconds = $appointment->getBookingEnd()->getValue()->getTimestamp() -
            $appointment->getBookingEnd()->getValue()->getTimestamp();
        $bufferTimeBefore = $appointment->getService() && $appointment->getService()->getTimeBefore()
            ? $appointment->getService()->getTimeBefore()->getValue()
            : 0;
        $bufferTimeAfter  = $appointment->getService() && $appointment->getService()->getTimeAfter()
            ? $appointment->getService()->getTimeAfter()->getValue()
            : 0;

        $startWithoutBuffer = $appointment->getBookingStart()->getValue();
        $start = (clone($startWithoutBuffer))->modify('-' . $bufferTimeBefore . 'seconds');

        $endWithoutBuffer = $appointment->getBookingEnd()->getValue();
        $end = (clone($endWithoutBuffer))->modify($bufferTimeAfter . 'seconds');

        return [
            'uuid'                    => $appointment->getId()->getValue(),
            'id'                      => $appointment->getId()->getValue(),
            'bookings'                => $appointment->getBookings()->toArray(),
            'serviceName'             => $appointment->getService()->getName()->getValue(),
            'employeeName'            => $appointment->getProvider()->getFullName(),
            'locationName'            => $appointment->getLocation() ? $appointment->getLocation()->getName()->getValue() : '',
            'start'                   => $start->format('Y-m-d H:i:s'),
            'end'                     => $end->format('Y-m-d H:i:s'),
            'startWithoutBuffer'      => $startWithoutBuffer->format('Y-m-d H:i:s'),
            'endWithoutBuffer'        => $endWithoutBuffer->format('Y-m-d H:i:s'),
            'mainColor'               => $appointment->getService()->getColor()->getValue(),
            'numberOfSlots'           => $appointmentDurationInSeconds / $timeSlotStep,
            'serviceId'               => $appointment->getService()->getId()->getValue(),
            'employeeId'              => $appointment->getProvider()->getId()->getValue(),
            'locationId'              => $appointment->getLocation() ? $appointment->getLocation()->getId()->getValue() : null,
            'bufferTimeBefore'        => $bufferTimeBefore,
            'bufferTimeAfter'         => $bufferTimeAfter,
            'timeZone'                => $start->getTimeZone()->getName(),
            'notes'                   => $appointment->getInternalNotes() ?
                $appointment->getInternalNotes()->getValue()
                : '',
            'integrationCalendarType' => false,
            'type'                    => $appointment->getBookings()->length() === 1
                ? 'singleAppointment'
                : 'groupAppointment',
            'editable'                => $user->getType() === Entities::CUSTOMER
                ? $settingsService->getSetting('roles', 'allowCustomerReschedule')
                : (
                    $user->getType() === Entities::PROVIDER ?
                        $settingsService->getSetting('roles', 'allowWriteAppointments')
                        : true
                ),
        ];
    }

    private function eventFormatter(Event $eventEntity, EventPeriod $eventPeriod, array $queryParams): array
    {
        $periodStartDate = clone $eventPeriod->getPeriodStart()->getValue();
        $periodEndDate   = clone $eventPeriod->getPeriodEnd()->getValue();

        $title = $eventEntity->getName()->getValue();
        if (!empty($queryParams['showEmployeeName'])) {
            $title = $eventEntity->getOrganizer() ? $eventEntity->getOrganizer()->getFullName() : $title;
        }

        $event = [
            'uuid'               => $eventEntity->getId()->getValue(),
            'title'              => $title,
            'mainColor'          => $eventEntity->getColor() ? $eventEntity->getColor()->getValue() : '#1788FB',
            'type'               => 'event',
            'editable'           => false,
            'startWithoutBuffer' => $periodStartDate->format('Y-m-d H:i:s'),
            'endWithoutBuffer'   => $periodEndDate->format('Y-m-d H:i:s'),
            'timeZone'           => $periodStartDate->getTimeZone()->getName(),
            'notes'              => '',
            'locationName'       => $eventEntity->getLocation() ? $eventEntity->getLocation()->getName()->getValue() : '',
            'employeeName'       => $eventEntity->getOrganizer() ? $eventEntity->getOrganizer()->getFullName() : '',
        ];

        if (in_array($queryParams['view'], ['dayGridMonthSevenDays', 'dayGridMonth', 'dayGridMonthMobile'])) {
            $event['start'] = $periodStartDate->format('Y-m-d');
            $event['end']   = $periodEndDate->modify('+1 day')->format('Y-m-d');
        } else {
            $event['groupId']    = $eventPeriod->getId()->getValue();
            $event['startRecur'] = $periodStartDate->format('Y-m-d');
            $event['endRecur']   = $periodEndDate->modify('+1 day')->format('Y-m-d');
            $event['startTime']  = $periodStartDate->format('H:i:s');
            $event['endTime']    = $periodEndDate->format('H:i:s') === '00:00:00'
                ? '23:59:59'
                : $periodEndDate->format('H:i:s');
        }

        return $event;
    }

    private function blockTimeFormatter(BlockTime $blockTime): array
    {
        $startDate = $blockTime->getStartDate()->getValue();
        $endDate   = $blockTime->getEndDate()->getValue();

        return [
            'uuid'               => $blockTime->getId()->getValue(),
            'id'                 => $blockTime->getId()->getValue(),
            'title'              => $blockTime->getName()->getValue(),
            'type'               => 'blockTime',
            'editable'           => false,
            'start'              => $startDate->format('Y-m-d H:i:s'),
            'end'                => $endDate->format('Y-m-d H:i:s'),
            'startWithoutBuffer' => $startDate->format('Y-m-d H:i:s'),
            'endWithoutBuffer'   => $endDate->format('Y-m-d H:i:s'),
            'timeZone'           => $startDate->getTimezone()->getName(),
            'employeeName'       => $blockTime->getUser() ? $blockTime->getUser()->getFullName() : BackendStrings::get('all_employees'),
        ];
    }
}
