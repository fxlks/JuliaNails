<?php

namespace AmeliaBooking\Application\Commands\Booking\Appointment;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Booking\AppointmentApplicationService;
use AmeliaBooking\Application\Services\Helper\HelperService;
use AmeliaBooking\Application\Services\Payment\PaymentApplicationService;
use AmeliaBooking\Application\Services\User\ProviderApplicationService;
use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Common\Exceptions\AuthorizationException;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Bookable\Service\Service;
use AmeliaBooking\Domain\Entity\Booking\Appointment\Appointment;
use AmeliaBooking\Domain\Entity\Booking\Appointment\CustomerBooking;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Entity\User\AbstractUser;
use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Bookable\Service\ServiceRepository;
use AmeliaBooking\Infrastructure\Repository\Booking\Appointment\AppointmentRepository;
use AmeliaBooking\Infrastructure\Repository\Booking\Appointment\CustomerBookingRepository;
use AmeliaBooking\Infrastructure\Repository\User\ProviderRepository;
use DateTimeZone;

/**
 * Class GetAppointmentBookingsCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Booking\Appointment
 */
class GetAppointmentBookingsCommandHandler extends CommandHandler
{
    /**
     * @param GetAppointmentBookingsCommand $command
     *
     * @return CommandResult
     *
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     * @throws AccessDeniedException
     */
    public function handle(GetAppointmentBookingsCommand $command)
    {
        $result = new CommandResult();

        /** @var HelperService $helperService */
        $helperService = $this->container->get('application.helper.service');

        /** @var AppointmentRepository $appointmentRepository */
        $appointmentRepository = $this->container->get('domain.booking.appointment.repository');

        /** @var ServiceRepository $serviceRepository */
        $serviceRepository = $this->container->get('domain.bookable.service.repository');

        /** @var SettingsService $settingsDS */
        $settingsDS = $this->container->get('domain.settings.service');

        /** @var PaymentApplicationService $paymentAS */
        $paymentAS = $this->container->get('application.payment.service');

        /** @var AppointmentApplicationService $appointmentAS */
        $appointmentAS = $this->container->get('application.booking.appointment.service');

        /** @var ProviderRepository $providerRepository */
        $providerRepository = $this->container->get('domain.users.providers.repository');

        /** @var ProviderApplicationService $providerAS */
        $providerAS = $this->container->get('application.user.provider.service');


        $params = $command->getField('params');

        if (isset($params['dates']) && empty($params['dates'][0]) && empty($params['dates'][1])) {
            unset($params['dates']);
        }

        try {
            /** @var AbstractUser $user */
            $user = $command->getUserApplicationService()->authorization(null, $command->getCabinetType());
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
            $params['providers'] = [$user->getId()->getValue()];
        }

        $customerCountParams = [];
        if ($user && $user->getType() === Entities::CUSTOMER) {
            $customerCountParams['customers'] = [$user->getId()->getValue()];
            $params['customers'] = [$user->getId()->getValue()];
        }

        $entitiesIds = !empty($params['search']) ? $appointmentAS->getAppointmentEntitiesIdsBySearchString($params['search']) : [];

        $appointmentsIds = [];

        $helperService->convertDates($params);

        /** @var Collection $periodAppointments */
        $periodAppointments = $appointmentRepository->getPeriodAppointments(
            array_merge(
                $params,
                ['search' => $entitiesIds, 'searchTerm' => !empty($params['search']) ? $params['search'] : ''],
                ['skipBookings' => empty($params['customers']) && empty($entitiesIds['customers'])]
            ),
            $params['limit']
        );

        $serviceIds = [];

        /** @var Appointment $appointment */
        foreach ($periodAppointments->getItems() as $appointment) {
            $appointmentsIds[] = $appointment->getId()->getValue();
            $serviceIds[]      = $appointment->getServiceId()->getValue();
        }

        /** @var Collection $appointments */
        $appointments = new Collection();

        $customersNoShowCountIds = [];

        $noShowTagEnabled = $settingsDS->isFeatureEnabled('noShowTag');

        if ($appointmentsIds) {
            $appointments = $appointmentRepository->getFiltered(
                [
                    'ids'           => $appointmentsIds,
                    'withLocations' => true,
                    'sort'          => !empty($params['sort']) ? $params['sort'] : null,
                    'customers'     => $user && $user->getType() === Entities::CUSTOMER
                        ? [$user->getId()->getValue()]
                        : [],
                ]
            );
        }

        /** @var Collection $services */
        $services = $serviceRepository->getAllArrayIndexedById($serviceIds);

        $providersServices = $providerRepository->getProvidersServices($serviceIds);

        $appointmentsArray = [];

        /** @var Appointment $appointment */
        foreach ($appointments->getItems() as $appointment) {
            /** @var Service $service */
            $service = $services->getItem($appointment->getServiceId()->getValue());

            $providerId = $appointment->getProviderId()->getValue();

            // skip appointments for other providers if user is provider
            if (
                (!$readOthers) &&
                $user->getType() === Entities::PROVIDER &&
                $user->getId()->getValue() !== $providerId
            ) {
                continue;
            }

            $appointmentAS->calculateAndSetAppointmentEnd($appointment, $service);

            $timeZone = !empty($params['timeZone'])
                ? $params['timeZone']
                : ($user && $user->getType() === Entities::PROVIDER ? $providerAS->getTimeZone($user) : null);

            if ($timeZone) {
                $appointment->getBookingStart()->getValue()->setTimezone(new DateTimeZone($timeZone));

                $appointment->getBookingEnd()->getValue()->setTimezone(new DateTimeZone($timeZone));
            }

            $bookedSpots = 0;
            $bookings    = [];

            $isPackageAppointment = false;
            $bookingPrice         = 0;
            $paidPrice            = 0;
            $bookingSource        = 'backend';
            $frontendBookings     = 0;

            $wcTax = 0;
            $wcDiscount = 0;

            /** @var CustomerBooking $booking */
            foreach ($appointment->getBookings()->getItems() as $booking) {
                // fix for wrongly saved JSON
                if (
                    $booking->getCustomFields() &&
                    json_decode($booking->getCustomFields()->getValue(), true) === null
                ) {
                    $booking->setCustomFields(null);
                }

                if ($noShowTagEnabled && !in_array($booking->getCustomerId()->getValue(), $customersNoShowCountIds)) {
                    $customersNoShowCountIds[] = $booking->getCustomerId()->getValue();
                }

                $bookedSpots += $booking->getPersons()->getValue();

                $bookings[] = [
                    'id'       => $booking->getId()->getValue(),
                    'status'   => $booking->getStatus()->getValue(),
                    'customer' => $booking->getCustomer() ? $booking->getCustomer()->toArray() : null,
                    'payment'  => [
                        'paymentMethods' => array_map(
                            function ($payment) {
                                return $payment['gateway'];
                            },
                            $booking->getPayments()->toArray()
                        ),
                        'status' => $paymentAS->getFullStatus($booking->toArray(), 'appointment'),
                    ],
                    'created'  => $booking->getCreated() ?
                        $booking->getCreated()->getValue()->format('Y-m-d') : null,
                ];

                $isPackageAppointment = !empty($booking->getPackageCustomerService());

                $bookingPrice += $paymentAS->calculateAppointmentPrice($booking->toArray(), 'appointment');

                foreach ($booking->getPayments()->toArray() as $payment) {
                    if ($payment['status'] === 'paid' || $payment['status'] === 'partiallyPaid') {
                        $paidPrice += $payment['amount'];
                    }

                    $paymentAS->addWcFields($payment);

                    $wcTax += !empty($payment['wcItemTaxValue']) ? $payment['wcItemTaxValue'] : 0;

                    $wcDiscount += !empty($payment['wcItemCouponValue']) ? $payment['wcItemCouponValue'] : 0;
                }

                if ($booking->getInfo()) {
                    $bookingSource = 'frontendAndBackend';
                    $frontendBookings++;
                }
            }

            if ($frontendBookings === $appointment->getBookings()->length()) {
                $bookingSource = 'frontend';
            }

            $bookingStart = $appointment->getBookingStart() ? $appointment->getBookingStart()->getValue()->format('Y-m-d H:i:s') : null;
            $bookingEnd   = $appointment->getBookingEnd() ? $appointment->getBookingEnd()->getValue()->format('Y-m-d H:i:s') : null;

            $appointmentsArray[] = [
                'id' => $appointment->getId()->getValue(),
                'bookedSpots' => $bookedSpots,
                'bookingStartDateTime' => $bookingStart,
                'bookingEndDateTime' => $bookingEnd,
                'bookingStartDate' => $bookingStart ? explode(' ', $bookingStart)[0] : null,
                'bookingSource' => $bookingSource,
                'employee' => $appointment->getProvider() ? [
                    'id' => $appointment->getProvider()->getId()->getValue(),
                    'firstName' => $appointment->getProvider()->getFirstName() ? $appointment->getProvider()->getFirstName()->getValue() : null,
                    'lastName' => $appointment->getProvider()->getLastName() ? $appointment->getProvider()->getLastName()->getValue() : null,
                    'picture' => $appointment->getProvider()->getPicture() ? $appointment->getProvider()->getPicture()->getThumbPath() : null,
                    'badge' => $appointment->getProvider()->getBadgeId() ? $providerAS->getBadge($appointment->getProvider()->getBadgeId()->getValue()) : null,
                ] : null,
                'googleMeetLink' => $appointment->getGoogleMeetUrl(),
                'zoomHostLink' => $appointment->getZoomMeeting() ? $appointment->getZoomMeeting()->getStartUrl()->getValue() : null,
                'zoomJoinLink' => $appointment->getZoomMeeting() ? $appointment->getZoomMeeting()->getJoinUrl()->getValue() : null,
                'lessonSpace' => $appointment->getLessonSpace() ? $appointment->getLessonSpace() : null,
                'microsoftTeamsLink' => $appointment->getMicrosoftTeamsUrl() ? $appointment->getMicrosoftTeamsUrl() : null,
                'location' => $appointment->getLocation() ? [
                    'id' => $appointment->getLocation()->getId()->getValue(),
                    'name' => $appointment->getLocation()->getName()->getValue(),
                ] : null,
                'service' => $appointment->getService() ? [
                    'id' => $appointment->getService()->getId()->getValue(),
                    'name' => $appointment->getService()->getName()->getValue(),
                    'color' => $appointment->getService()->getColor() ? $service->getColor()->getValue() : null,
                    'picture' => $appointment->getService()->getPicture() ? $appointment->getService()->getPicture()->getThumbPath() : null,
                ] : null,
                'bookings' => $bookings,
                'type' => $appointment->getBookings()->length() > 1 ? 'groupBooking' : ($isPackageAppointment ? 'package' : 'standAlone'),
                'maxCapacity' => !empty($providersServices[$providerId][$service->getId()->getValue()]) ?
                    $providersServices[$providerId][$service->getId()->getValue()]['maxCapacity'] :
                    $service->getMaxCapacity()->getValue(),
                'status' => $appointment->getStatus() ? $appointment->getStatus()->getValue() : null,
                'note' => $appointment->getInternalNotes() ? $appointment->getInternalNotes()->getValue() : null,
                'price' => [
                    'total' => $bookingPrice + $wcTax - $wcDiscount,
                ],
                'paidPrice' => $paidPrice,
                'cancelable' => $appointmentAS->isCancelable($appointment, $service, $user),
                'reschedulable' => $appointmentAS->isReschedulable($appointment, $service, $user),
            ];
        }

        $periodsAppointmentsCount = $appointmentRepository->getPeriodAppointments(
            array_merge_recursive(
                $params,
                ['search' => $entitiesIds, 'searchTerm' => !empty($params['search']) ? $params['search'] : ''],
                ['skipBookings' => empty($params['customers']) && empty($entitiesIds['customers'])]
            )
        );

        $periodsAppointmentsTotalCount = $appointmentRepository->getPeriodAppointments(
            array_merge($customerCountParams, $providerCountParams, ['skipBookings' => true])
        );

        if ($noShowTagEnabled && $customersNoShowCountIds) {
            /** @var CustomerBookingRepository $bookingRepository */
            $bookingRepository = $this->container->get('domain.booking.customerBooking.repository');

            $customersNoShowCount = $bookingRepository->countByNoShowStatus($customersNoShowCountIds);

            foreach ($appointmentsArray as &$appointmentArray) {
                foreach ($appointmentArray['bookings'] as &$booking) {
                    if (!empty($customersNoShowCount[$booking['customer']['id']])) {
                        $booking['customer']['noShowCount'] = $customersNoShowCount[$booking['customer']['id']]['count'];
                    }
                }
            }
        }

        $appointmentsArray = apply_filters('amelia_get_appointments_filter', $appointmentsArray);

        do_action('amelia_get_appointments', $appointmentsArray);

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully retrieved appointments');

        $result->setData(
            [
                Entities::APPOINTMENTS => $appointmentsArray,
                'totalCount'           => $periodsAppointmentsTotalCount->length(),
                'filteredCount'        => $periodsAppointmentsCount->length(),
            ]
        );

        return $result;
    }
}
