<?php

namespace AmeliaBooking\Application\Commands\Booking\Appointment;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Bookable\AbstractPackageApplicationService;
use AmeliaBooking\Application\Services\Booking\AppointmentApplicationService;
use AmeliaBooking\Application\Services\Booking\BookingApplicationService;
use AmeliaBooking\Application\Services\Helper\HelperService;
use AmeliaBooking\Application\Services\User\ProviderApplicationService;
use AmeliaBooking\Application\Services\User\UserApplicationService;
use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Common\Exceptions\AuthorizationException;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Bookable\Service\Service;
use AmeliaBooking\Domain\Entity\Booking\Appointment\Appointment;
use AmeliaBooking\Domain\Entity\Booking\Appointment\CustomerBooking;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Entity\User\AbstractUser;
use AmeliaBooking\Domain\Services\DateTime\DateTimeService;
use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Domain\ValueObjects\String\BookingStatus;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Bookable\Service\ServiceRepository;
use AmeliaBooking\Infrastructure\Repository\Booking\Appointment\AppointmentRepository;
use AmeliaBooking\Infrastructure\Repository\Booking\Appointment\CustomerBookingRepository;
use DateTimeZone;
use Interop\Container\Exception\ContainerException;

/**
 * Class GetAppointmentsCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Booking\Appointment
 */
class GetAppointmentsCommandHandler extends CommandHandler
{
    /**
     * @param GetAppointmentsCommand $command
     *
     * @return CommandResult
     *
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     * @throws AccessDeniedException
     * @throws ContainerException
     */
    public function handle(GetAppointmentsCommand $command)
    {
        $result = new CommandResult();

        /** @var HelperService $helperService */
        $helperService = $this->container->get('application.helper.service');

        /** @var AppointmentRepository $appointmentRepository */
        $appointmentRepository = $this->container->get('domain.booking.appointment.repository');

        /** @var ServiceRepository $serviceRepository */
        $serviceRepository = $this->container->get('domain.bookable.service.repository');

        /** @var AbstractPackageApplicationService $packageAS */
        $packageAS = $this->container->get('application.bookable.package');

        /** @var SettingsService $settingsDS */
        $settingsDS = $this->container->get('domain.settings.service');

        /** @var UserApplicationService $userAS */
        $userAS = $this->container->get('application.user.service');

        /** @var BookingApplicationService $bookingAS */
        $bookingAS = $this->container->get('application.booking.booking.service');

        /** @var AppointmentApplicationService $appointmentAS */
        $appointmentAS = $this->container->get('application.booking.appointment.service');

        /** @var ProviderApplicationService $providerAS */
        $providerAS = $this->container->get('application.user.provider.service');

        $params = $command->getField('params');

        $isCabinetPage = $command->getPage() === 'cabinet';

        $isCalendarPage = $command->getPage() === 'calendar';

        $isCabinetPackageRequest = $isCabinetPage && isset($params['activePackages']);

        $isDashboardPackageRequest = !$isCabinetPage && (isset($params['packageId']) || !empty($params['packageBookings']));

        try {
            /** @var AbstractUser $user */
            $user = $command->getUserApplicationService()->authorization($isCabinetPage ? $command->getToken() : null, $command->getCabinetType());
        } catch (AuthorizationException $e) {
            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setData(
                [
                    'reauthorize' => true
                ]
            );

            return $result;
        }

        $readOthers = $this->container->getPermissionsService()->currentUserCanReadOthers(Entities::APPOINTMENTS);

        $providerCountParams = [];
        if (
            (!$readOthers) &&
            $user && $user->getType() === Entities::PROVIDER
        ) {
            $providerCountParams['providerId'] = $user->getId()->getValue();
        }

        // TODO: Redesign - replace 'customerId' parameter with 'customers' on all /appointments calls and remove this part
        if (!empty($params['customerId'])) {
            $params['customers'] = $params['customerId'];
        }

        $customerCountParams = [];
        if ($user && $user->getType() === Entities::CUSTOMER) {
            $customerCountParams['customers'] = [$user->getId()->getValue()];
            $params['customers'] = [$user->getId()->getValue()];
        }

        $helperService->convertDates($params);

        $entitiesIds = !empty($params['search']) && !$isCabinetPackageRequest ?
            $appointmentAS->getAppointmentEntitiesIdsBySearchString($params['search']) : [];


        $countParams = $params;

        $appointmentsIds = [];

        if (
            !empty($params['search']) &&
            !$entitiesIds['customers'] &&
            !$entitiesIds['services'] &&
            !$entitiesIds['providers']
        ) {
            $result->setResult(CommandResult::RESULT_SUCCESS);
            $result->setMessage('Successfully retrieved appointments');
            $result->setData(
                [
                    Entities::APPOINTMENTS     => [],
                    'availablePackageBookings' => [],
                    'occupied'                 => [],
                    'total'                    => 0,
                    'totalApproved'            => 0,
                    'totalPending'             => 0,
                    'totalCount'               => $appointmentRepository->getPeriodAppointmentsCount(array_merge($providerCountParams, $customerCountParams)),
                    'filteredCount'            => 0,
                ]
            );

            return $result;
        }

        $availablePackageBookings = [];

        if (!$isCabinetPackageRequest && !$isDashboardPackageRequest) {
            /** @var Collection $periodAppointments */
            $periodAppointments = $appointmentRepository->getPeriodAppointments(
                array_merge(
                    [
                        'customers' => $isCabinetPage && $user->getType() === Entities::CUSTOMER ?
                            [$user->getId()->getValue()] : [],
                        'providers' => $user && $user->getType() === Entities::PROVIDER ?
                            [$user->getId()->getValue()] : [],
                    ],
                    array_merge($params, ['endsInDateRange' => $isCalendarPage]),
                    $entitiesIds,
                    ['skipBookings' => !$isCabinetPage && empty($params['customers']) && empty($entitiesIds['customers'])]
                ),
                $settingsDS->getSetting('general', 'itemsPerPage')
            );

            /** @var Appointment $appointment */
            foreach ($periodAppointments->getItems() as $appointment) {
                $appointmentsIds[] = $appointment->getId()->getValue();
            }
        }

        /** @var Collection $appointments */
        $appointments = new Collection();

        $customersNoShowCountIds = [];

        $noShowTagEnabled = $settingsDS->isFeatureEnabled('noShowTag');

        if (!$isCabinetPackageRequest && $appointmentsIds) {
            $appointments = $appointmentRepository->getFiltered(
                [
                    'ids'           => $appointmentsIds,
                    'skipServices'  => isset($params['skipServices']) ? $params['skipServices'] : false,
                    'skipProviders' => isset($params['skipProviders']) ? $params['skipProviders'] : false,
                    'joinPackages'  => $isCabinetPage
                ]
            );
        } elseif ($isDashboardPackageRequest || ($user && $user->getId() && $isCabinetPackageRequest)) {
            $availablePackageBookings = $packageAS->getPackageAvailability(
                $appointments,
                [
                    'purchased'  => !empty($params['dates']) ? $params['dates'] : [],
                    'customers'  => $isCabinetPackageRequest ? [$user->getId()->getValue()] : (!empty($params['customers']) ? $params['customers'] : null),
                    'packageId'  => !$isCabinetPackageRequest && !empty($params['packageId']) ?
                        (int)$params['packageId'] : null,
                ]
            );

            if ($noShowTagEnabled && !!$availablePackageBookings) {
                $customersNoShowCountIds[] = $availablePackageBookings[0]['customerId'];
            }
        }

        /** @var Collection $services */
        $services = $serviceRepository->getAllArrayIndexedById();

        if (!$isCabinetPage) {
            $packageAS->setPackageBookingsForAppointments($appointments);
        }

        $occupiedTimes = [];

        $currentDateTime = DateTimeService::getNowDateTimeObject();

        $groupedAppointments = [];

        /** @var Appointment $appointment */
        foreach ($appointments->getItems() as $appointment) {
            /** @var Service $service */
            $service = $services->getItem($appointment->getServiceId()->getValue());

            $bookingsCount = 0;

            /** @var CustomerBooking $booking */
            foreach ($appointment->getBookings()->getItems() as $booking) {
                // fix for wrongly saved JSON
                if (
                    $booking->getCustomFields() &&
                    json_decode($booking->getCustomFields()->getValue(), true) === null
                ) {
                    $booking->setCustomFields(null);
                }

                if ($bookingAS->isBookingApprovedOrPending($booking->getStatus()->getValue())) {
                    $bookingsCount++;
                }

                if ($noShowTagEnabled && !in_array($booking->getCustomerId()->getValue(), $customersNoShowCountIds)) {
                    $customersNoShowCountIds[] = $booking->getCustomerId()->getValue();
                }
            }

            $providerId = $appointment->getProviderId()->getValue();

            $isGroup = false;

            // skip appointments/bookings for other customers if user is customer, and remember that time/date values
            if ($userAS->isCustomer($user)) {
                /** @var CustomerBooking $booking */
                foreach ($appointment->getBookings()->getItems() as $bookingKey => $booking) {
                    if (!$user->getId() || $booking->getCustomerId()->getValue() !== $user->getId()->getValue()) {
                        /** @var CustomerBooking $otherBooking */
                        $otherBooking = $appointment->getBookings()->getItem($bookingKey);

                        if (
                            $otherBooking->getStatus()->getValue() === BookingStatus::APPROVED ||
                            $otherBooking->getStatus()->getValue() === BookingStatus::PENDING
                        ) {
                            $isGroup = true;
                        }

                        $appointment->getBookings()->deleteItem($bookingKey);
                    }
                }

                if ($appointment->getBookings()->length() === 0) {
                    $serviceTimeBefore = $service->getTimeBefore() ?
                        $service->getTimeBefore()->getValue() : 0;

                    $serviceTimeAfter = $service->getTimeAfter() ?
                        $service->getTimeAfter()->getValue() : 0;

                    $occupiedTimeStart = DateTimeService::getCustomDateTimeObject(
                        $appointment->getBookingStart()->getValue()->format('Y-m-d H:i:s')
                    )->modify('-' . $serviceTimeBefore . ' second')->format('H:i:s');

                    $occupiedTimeEnd = DateTimeService::getCustomDateTimeObject(
                        $appointment->getBookingEnd()->getValue()->format('Y-m-d H:i:s')
                    )->modify('+' . $serviceTimeAfter . ' second')->format('H:i:s');

                    $occupiedTimes[$appointment->getBookingStart()->getValue()->format('Y-m-d')][] =
                        [
                            'employeeId' => $providerId,
                            'startTime'  => $occupiedTimeStart,
                            'endTime'    => $occupiedTimeEnd,
                        ];

                    continue;
                }
            }

            // skip appointments for other providers if user is provider
            if (
                (!$readOthers || $isCabinetPage) &&
                $user->getType() === Entities::PROVIDER &&
                $user->getId()->getValue() !== $providerId
            ) {
                continue;
            }

            $appointmentAS->calculateAndSetAppointmentEnd($appointment, $service);

            $minimumCancelTimeInSeconds = $settingsDS
                ->getEntitySettings($service->getSettings())
                ->getGeneralSettings()
                ->getMinimumTimeRequirementPriorToCanceling();

            $minimumCancelTime = DateTimeService::getCustomDateTimeObject(
                $appointment->getBookingStart()->getValue()->format('Y-m-d H:i:s')
            )->modify("-{$minimumCancelTimeInSeconds} seconds");

            $date = $appointment->getBookingStart()->getValue()->format('Y-m-d');

            $cancelable = $currentDateTime <= $minimumCancelTime;

            $minimumRescheduleTimeInSeconds = $settingsDS
                ->getEntitySettings($service->getSettings())
                ->getGeneralSettings()
                ->getMinimumTimeRequirementPriorToRescheduling();

            $minimumRescheduleTime = DateTimeService::getCustomDateTimeObject(
                $appointment->getBookingStart()->getValue()->format('Y-m-d H:i:s')
            )->modify("-{$minimumRescheduleTimeInSeconds} seconds");

            $reschedulable = $currentDateTime <= $minimumRescheduleTime;


            if ($isCabinetPage || ($user && $user->getType() === Entities::PROVIDER)) {
                $timeZone = !empty($params['timeZone'])
                    ? $params['timeZone']
                    : ($user && $user->getType() === Entities::PROVIDER ? $providerAS->getTimeZone($user) : 'UTC');

                $appointment->getBookingStart()->getValue()->setTimezone(new DateTimeZone($timeZone));

                $appointment->getBookingEnd()->getValue()->setTimezone(new DateTimeZone($timeZone));

                $date = $appointment->getBookingStart()->getValue()->format('Y-m-d');

                foreach ($availablePackageBookings as &$packageCustomerPurchase) {
                    foreach ($packageCustomerPurchase['packages'] as &$packagePurchase) {
                        foreach ($packagePurchase['services'] as &$packageService) {
                            foreach ($packageService['bookings'] as &$purchase) {
                                $purchasedDate = DateTimeService::getCustomDateTimeObjectInTimeZone(
                                    $purchase['purchased'],
                                    $timeZone
                                );

                                $purchase['purchased'] = $purchasedDate->format('Y-m-d H:i');
                            }
                        }
                    }
                }
            }

            $groupedAppointments[$date]['date'] = $date;

            $groupedAppointments[$date]['appointments'][] = array_merge(
                $appointment->toArray(),
                [
                    'cancelable'    => $cancelable,
                    'reschedulable' => $reschedulable,
                    'past'          => $currentDateTime >= $appointment->getBookingStart()->getValue(),
                    'isGroup'       => $isGroup,
                ]
            );
        }

        $emptyBookedPackages = null;

        if (
            !empty($params['packageId']) &&
            empty($params['services']) &&
            empty($params['providers']) &&
            empty($params['locations'])
        ) {
            /** @var AbstractPackageApplicationService $packageApplicationService */
            $packageApplicationService = $this->container->get('application.bookable.package');

            /** @var Collection $emptyBookedPackages */
            $emptyBookedPackages = $packageApplicationService->getEmptyPackages(
                [
                    'packages'   => [$params['packageId']],
                    'purchased'  => !empty($params['dates']) ? $params['dates'] : [],
                    'customers'  => !empty($params['customers']) ? $params['customers'] : null,
                ]
            );
        }

        $periodsAppointmentsCount = 0;

        $periodsAppointmentsApprovedCount = 0;

        $periodsAppointmentsPendingCount = 0;

        $periodsAppointmentsTotalCount = 0;

        if (!empty($countParams['page']) || (!$isCabinetPackageRequest && !$isCabinetPage && !$isCalendarPage)) {
            if (
                (!$readOthers) &&
                $user->getType() === Entities::PROVIDER
            ) {
                $countParams['providerId'] = $user->getId()->getValue();
            }
            $periodsAppointmentsCount = $appointmentRepository->getPeriodAppointmentsCount(
                array_merge($countParams, $providerCountParams, $entitiesIds)
            );

            $periodsAppointmentsTotalCount = $appointmentRepository->getPeriodAppointmentsCount(array_merge($providerCountParams, $customerCountParams));

            $periodsAppointmentsApprovedCount = $appointmentRepository->getPeriodAppointmentsCount(
                array_merge(
                    $countParams,
                    $providerCountParams,
                    ['status' => BookingStatus::APPROVED],
                    $entitiesIds
                )
            );

            $periodsAppointmentsPendingCount = $appointmentRepository->getPeriodAppointmentsCount(
                array_merge(
                    $countParams,
                    $providerCountParams,
                    ['status' => BookingStatus::PENDING],
                    $entitiesIds
                )
            );
        }

        $customersNoShowCount = [];

        if ($noShowTagEnabled && $customersNoShowCountIds) {
            /** @var CustomerBookingRepository $bookingRepository */
            $bookingRepository = $this->container->get('domain.booking.customerBooking.repository');

            $customersNoShowCount = $bookingRepository->countByNoShowStatus($customersNoShowCountIds);
        }

        $groupedAppointments = apply_filters('amelia_get_appointments_filter', $groupedAppointments);

        do_action('amelia_get_appointments', $groupedAppointments);


        // TODO: Redesign - remove total, totalApproved, totalPending
        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully retrieved appointments');
        $result->setData(
            [
                Entities::APPOINTMENTS     =>
                !empty($params['asArray']) && filter_var($params['asArray'], FILTER_VALIDATE_BOOLEAN) ?
                    $appointments->toArray() :
                    $groupedAppointments,
                'availablePackageBookings' => $availablePackageBookings,
                'emptyPackageBookings'     => !empty($emptyBookedPackages) ? $emptyBookedPackages->toArray() : [],
                'occupied'                 => $occupiedTimes,
                'total'                    => $periodsAppointmentsCount,
                'totalApproved'            => $periodsAppointmentsApprovedCount,
                'totalPending'             => $periodsAppointmentsPendingCount,
                'totalCount'               => $periodsAppointmentsTotalCount,
                'filteredCount'            => $periodsAppointmentsCount,
                'currentUser'              => $user ? $user->toArray() : null,
                'customersNoShowCount'     => $customersNoShowCount ? array_values($customersNoShowCount) : []
            ]
        );

        return $result;
    }
}
