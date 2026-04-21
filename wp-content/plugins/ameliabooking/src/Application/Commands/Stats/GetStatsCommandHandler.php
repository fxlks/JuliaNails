<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Application\Commands\Stats;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Bookable\AbstractPackageApplicationService;
use AmeliaBooking\Application\Services\Booking\EventApplicationService;
use AmeliaBooking\Application\Services\Stats\StatsService;
use AmeliaBooking\Application\Services\User\ProviderApplicationService;
use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Bookable\Service\Package;
use AmeliaBooking\Domain\Entity\Bookable\Service\Service;
use AmeliaBooking\Domain\Entity\Booking\Event\Event;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Entity\User\Provider;
use AmeliaBooking\Domain\Services\DateTime\DateTimeService;
use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Domain\Services\User\ProviderService;
use AmeliaBooking\Domain\ValueObjects\String\BookingStatus;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Bookable\Service\ServiceRepository;
use AmeliaBooking\Infrastructure\Repository\Booking\Appointment\CustomerBookingRepository;
use AmeliaBooking\Infrastructure\Repository\User\ProviderRepository;
use Exception;
use Interop\Container\Exception\ContainerException;
use Slim\Exception\ContainerValueNotFoundException;

/**
 * Class GetStatsCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Stats
 */
class GetStatsCommandHandler extends CommandHandler
{
    /**
     * @param GetStatsCommand $command
     *
     * @return CommandResult
     * @throws ContainerValueNotFoundException
     * @throws AccessDeniedException
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     * @throws Exception
     * @throws ContainerException
     */
    public function handle(GetStatsCommand $command)
    {
        if (!$command->getPermissionService()->currentUserCanRead(Entities::DASHBOARD)) {
            throw new AccessDeniedException('You are not allowed to read coupons.');
        }

        $result = new CommandResult();

        /** @var ServiceRepository $serviceRepository */
        $serviceRepository = $this->container->get('domain.bookable.service.repository');
        /** @var ProviderRepository $providerRepository */
        $providerRepository = $this->container->get('domain.users.providers.repository');

        /** @var StatsService $statsAS */
        $statsAS = $this->container->get('application.stats.service');
        /** @var SettingsService $settingsDS */
        $settingsDS = $this->container->get('domain.settings.service');
        /** @var AbstractPackageApplicationService $packageAS */
        $packageAS = $this->container->get('application.bookable.package');
        /** @var ProviderApplicationService $providerAS */
        $providerAS = $this->container->get('application.user.provider.service');
        /** @var EventApplicationService $eventAS */
        $eventAS = $this->container->get('application.booking.event.service');
        /** @var ProviderService $providerDS */
        $providerDS = $this->container->get('domain.user.provider.service');

        $params = $command->getField('params');

        $startDate = $params['dates'][0] . ' 00:00:00';

        $endDate = $params['dates'][1] . ' 23:59:59';

        $previousPeriodStart = DateTimeService::getCustomDateTimeObject($startDate);

        $previousPeriodEnd = DateTimeService::getCustomDateTimeObject($endDate);

        $numberOfDays = $previousPeriodEnd->diff($previousPeriodStart)->days + 1;

        $entities = [
            'providers' => [],
            'services'  => [],
            'packages'  => [],
            'events'    => [],
        ];

        $pastDates = [
            $previousPeriodStart->modify("-{$numberOfDays} day")->format('Y-m-d H:i:s'),
            $previousPeriodEnd->modify("-{$numberOfDays} day")->format('Y-m-d H:i:s'),
        ];

        $past = isset($params['past']) ? (int)$params['past'] : true;

        $stats = !empty($params) ? $params['stats'] : [];

        $selectedEventsPeriodStatistics = [];

        $previousEventsPeriodStatistics = [];

        $eventsNewCustomers = 0;

        $eventsReturningCustomers = 0;

        $eventsPastCustomers = 0;

        if (!$stats || in_array('events', $stats)) {
            /** @var Collection $events */
            $events = $eventAS->getEventsByCriteria(
                [
                    'id'        => !empty($params['events']) ? $params['events'] : [],
                    'dates'     => [$startDate, $endDate],
                    'providers' => !empty($params['providers']) ? $params['providers'] : [],
                    'tag'       => !empty($params['tag']) ? $params['tag'] : [],
                    'status'    => BookingStatus::APPROVED,
                ],
                [
                    'fetchEventsPeriods'    => true,
                    'fetchEventsTickets'    => true,
                    'fetchBookings'         => true,
                    'fetchBookingsTickets'  => true,
                    'fetchBookingsPayments' => true,
                    'fetchApprovedBookings' => true,
                ],
                0
            );

            $selectedEventsPeriodStatistics = $statsAS->getEventsRangeStatisticsData(
                $events,
                $startDate,
                $endDate
            );

            $eventsCustomersIds = [];

            foreach ($selectedEventsPeriodStatistics as $statsData) {
                foreach (!empty($statsData['customers']) ? $statsData['customers'] : [] as $id => $count) {
                    $eventsCustomersIds = array_unique(
                        array_merge($eventsCustomersIds, [$id])
                    );
                }
            }

            /** @var Event $event */
            foreach ($events->getItems() as $event) {
                $entities['events'][$event->getId()->getValue()] = [
                    'name'  => $event->getName()->getValue(),
                    'photo' => $event->getPicture()
                        ? $event->getPicture()->getThumbPath()
                        : null,
                ];
            }

            if ($past) {
                /** @var Collection $previousEvents */
                $previousEvents = $eventAS->getEventsByCriteria(
                    [
                        'id'        => !empty($params['events']) ? $params['events'] : [],
                        'dates'     => $pastDates,
                        'providers' => !empty($params['providers']) ? $params['providers'] : [],
                        'tag'       => !empty($params['tag']) ? $params['tag'] : [],
                        'status'    => BookingStatus::APPROVED,
                    ],
                    [
                        'fetchEventsPeriods'    => true,
                        'fetchEventsTickets'    => true,
                        'fetchBookings'         => true,
                        'fetchBookingsTickets'  => true,
                        'fetchBookingsPayments' => true,
                        'fetchApprovedBookings' => true,
                    ],
                    0
                );

                $previousEventsPeriodStatistics = $statsAS->getEventsRangeStatisticsData(
                    $previousEvents,
                    $pastDates[0],
                    $pastDates[1]
                );

                $eventsPastCustomersIds = [];

                foreach ($previousEventsPeriodStatistics as $statsData) {
                    foreach (!empty($statsData['customers']) ? $statsData['customers'] : [] as $id => $count) {
                        $eventsPastCustomersIds = array_unique(
                            array_merge($eventsPastCustomersIds, [$id])
                        );
                    }
                }

                $eventsPastCustomers = count($eventsPastCustomersIds);

                $eventsReturningCustomers = count(array_intersect($eventsCustomersIds, $eventsPastCustomersIds));

                $eventsNewCustomers = count($eventsCustomersIds) - $eventsReturningCustomers;
            }
        }

        $selectedAppointmentsPeriodStatistics = [];

        $previousAppointmentsPeriodStatistics = [];

        $appointmentsNewCustomers = 0;

        $appointmentsReturningCustomers = 0;

        $appointmentsPastCustomers = 0;

        if (!$stats || in_array('appointments', $stats)) {
            $appointmentStatsParams = [
                'dates'     => [$startDate, $endDate],
                'status'    => BookingStatus::APPROVED,
                'providers' => !empty($params['providers']) ? $params['providers'] : [],
                'services'  => !empty($params['services']) ? $params['services'] : [],
                'locations' => !empty($params['locations']) ? $params['locations'] : [],
            ];

            /** @var Collection $services */
            $services = $serviceRepository->getAllArrayIndexedById();

            /** @var Collection $packages */
            $packages = $packageAS->getPackages();

            /** @var Collection $selectedProviders */
            $selectedProviders = $providerRepository->getWithSchedule(
                [
                    'dates'     => $appointmentStatsParams['dates'],
                    'providers' => $appointmentStatsParams['providers'],
                ],
                false
            );

            $providerDS->filterProvidersAndScheduleByCriteria($selectedProviders, $params);

            // Statistic
            $selectedAppointmentsPeriodStatistics = $statsAS->getAppointmentsRangeStatisticsData(
                $appointmentStatsParams,
                $services,
                $selectedProviders
            );

            $servicesIds = [];

            $appointmentsCustomersIds = [];

            foreach ($selectedAppointmentsPeriodStatistics as $statsData) {
                foreach (!empty($statsData['providers']) ? $statsData['providers'] : [] as $id => $entityData) {
                    if (empty($entities['providers'][$id])) {
                        /** @var Provider $provider */
                        $provider = $selectedProviders->getItem($id);

                        $entities['providers'][$id] = [
                            'name'  => $provider->getFullName(),
                            'photo' => $provider->getPicture()
                                ? $provider->getPicture()->getThumbPath()
                                : null,
                            'badge' => $provider->getBadgeId()
                                ? $providerAS->getBadge($provider->getBadgeId()->getValue())
                                : null,
                        ];
                    }

                    foreach ($entityData['intervals'] as $interval) {
                        $servicesIds = array_unique(
                            array_merge($servicesIds, $interval['services'])
                        );
                    }
                }

                foreach (!empty($statsData['services']) ? $statsData['services'] : [] as $id => $entityData) {
                    $servicesIds = array_unique(
                        array_merge($servicesIds, [$id])
                    );
                }

                foreach (!empty($statsData['packages']) ? $statsData['packages'] : [] as $id => $entityData) {
                    if (empty($entities['packages'][$id])) {
                        /** @var Package $package */
                        $package = $packages->getItem($id);

                        $entities['packages'][$id] = [
                            'name'  => $package->getName()->getValue(),
                            'photo' => $package->getPicture()
                                ? $package->getPicture()->getThumbPath()
                                : null,
                        ];
                    }
                }

                foreach (!empty($statsData['customers']) ? $statsData['customers'] : [] as $id => $count) {
                    $appointmentsCustomersIds = array_unique(
                        array_merge($appointmentsCustomersIds, [$id])
                    );
                }
            }

            /** @var Package $package */
            foreach ($packages->getItems() as $package) {
                if (empty($entities['packages'][$package->getId()->getValue()])) {
                    $entities['packages'][$package->getId()->getValue()] = [
                        'name'  => $package->getName()->getValue(),
                        'photo' => $package->getPicture()
                            ? $package->getPicture()->getThumbPath()
                            : null,
                    ];
                }
            }

            foreach ($servicesIds as $id) {
                if (empty($entities['services'][$id])) {
                    /** @var Service $service */
                    $service = $services->getItem($id);

                    $entities['services'][$id] = [
                        'name'  => $service->getName()->getValue(),
                        'photo' => $service->getPicture()
                            ? $service->getPicture()->getThumbPath()
                            : null,
                    ];
                }
            }

            if ($past) {
                /** @var Collection $pastProviders */
                $pastProviders = $providerRepository->getWithSchedule(
                    [
                        'dates'     => $pastDates,
                        'providers' => $appointmentStatsParams['providers'],
                    ],
                    false
                );

                $providerDS->filterProvidersAndScheduleByCriteria($pastProviders, $params);

                $previousAppointmentsPeriodStatistics = $statsAS->getAppointmentsRangeStatisticsData(
                    array_merge(
                        $appointmentStatsParams,
                        [
                            'dates' => $pastDates,
                        ]
                    ),
                    $services,
                    $pastProviders
                );

                $appointmentsPastCustomersIds = [];

                foreach ($previousAppointmentsPeriodStatistics as $statsData) {
                    foreach (!empty($statsData['customers']) ? $statsData['customers'] : [] as $id => $count) {
                        $appointmentsPastCustomersIds = array_unique(
                            array_merge($appointmentsPastCustomersIds, [$id])
                        );
                    }
                }

                $appointmentsPastCustomers = count($appointmentsPastCustomersIds);

                $appointmentsReturningCustomers = count(array_intersect($appointmentsCustomersIds, $appointmentsPastCustomersIds));

                $appointmentsNewCustomers = count($appointmentsCustomersIds) - $appointmentsReturningCustomers;
            }
        }

        $selectedPeriodStatistics = [];
        $previousPeriodStatistics = [];

        foreach ($selectedAppointmentsPeriodStatistics as $key => $value) {
            $selectedPeriodStatistics[$key] = $value;
        }

        foreach ($previousAppointmentsPeriodStatistics as $key => $value) {
            $previousPeriodStatistics[$key] = $value;
        }

        foreach ($selectedEventsPeriodStatistics as $key => $value) {
            $selectedPeriodStatistics[$key] = array_merge(
                !empty($selectedPeriodStatistics[$key]) ? $selectedPeriodStatistics[$key] : [],
                $value ?: []
            );
        }

        foreach ($previousEventsPeriodStatistics as $key => $value) {
            $previousPeriodStatistics[$key] = array_merge(
                !empty($previousPeriodStatistics[$key]) ? $previousPeriodStatistics[$key] : [],
                $value ?: []
            );
        }

        $customersNoShowCount = [];

        $customersNoShowCountIds = [];

        $noShowTagEnabled = $settingsDS->getSetting('roles', 'enableNoShowTag');

        if ($noShowTagEnabled && $customersNoShowCountIds) {
            /** @var CustomerBookingRepository $bookingRepository */
            $bookingRepository = $this->container->get('domain.booking.customerBooking.repository');

            $customersNoShowCount = $bookingRepository->countByNoShowStatus($customersNoShowCountIds);
        }


        $selectedPeriodStatistics = apply_filters('amelia_get_stats_filter', $selectedPeriodStatistics);

        do_action('amelia_get_stats', $selectedPeriodStatistics);

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully retrieved stats.');
        $result->setData(
            [
                'selectedPeriodStats'  => $selectedPeriodStatistics,
                'previousPeriodStats'  => $previousPeriodStatistics,
                'employeesStats'       => !$stats || in_array('employees', $stats)
                    ? $statsAS->getEmployeesStats(['dates' => [$startDate, $endDate]])
                    : [],
                'servicesStats'        => !$stats || in_array('services', $stats)
                    ? $statsAS->getServicesStats(['dates' => [$startDate, $endDate]])
                    : [],
                'locationsStats'       => !$stats || in_array('locations', $stats)
                    ? $statsAS->getLocationsStats(['dates' => [$startDate, $endDate]])
                    : [],
                'customersStats'       => !$stats || in_array('customers', $stats)
                    ? [
                        'newCustomersCount'        => $appointmentsNewCustomers + $eventsNewCustomers,
                        'returningCustomersCount'  => $appointmentsReturningCustomers + $eventsReturningCustomers,
                        'totalPastPeriodCustomers' => $appointmentsPastCustomers + $eventsPastCustomers,
                    ]
                    : [],
                'customersNoShowCount' => $customersNoShowCount ? array_values($customersNoShowCount) : [],
                'entities'             => $entities,
            ]
        );

        return $result;
    }
}
