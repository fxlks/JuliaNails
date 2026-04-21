<?php

namespace AmeliaBooking\Application\Commands\Booking\Appointment;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Booking\AppointmentApplicationService;
use AmeliaBooking\Application\Services\CustomField\AbstractCustomFieldApplicationService;
use AmeliaBooking\Application\Services\Payment\PaymentApplicationService;
use AmeliaBooking\Application\Services\Reservation\AppointmentReservationService;
use AmeliaBooking\Application\Services\User\CustomerApplicationService;
use AmeliaBooking\Application\Services\User\ProviderApplicationService;
use AmeliaBooking\Application\Services\User\UserApplicationService;
use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Common\Exceptions\AuthorizationException;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Booking\Appointment\Appointment;
use AmeliaBooking\Domain\Entity\Booking\Appointment\CustomerBooking;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Entity\Payment\Payment;
use AmeliaBooking\Domain\Entity\User\AbstractUser;
use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Infrastructure\Common\Exceptions\NotFoundException;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Bookable\Service\ServiceRepository;
use AmeliaBooking\Infrastructure\Repository\Booking\Appointment\AppointmentRepository;
use AmeliaBooking\Infrastructure\Repository\CustomField\CustomFieldRepository;
use AmeliaBooking\Infrastructure\Repository\Payment\PaymentRepository;
use AmeliaBooking\Infrastructure\Services\LessonSpace\AbstractLessonSpaceService;
use DateTimeZone;
use Slim\Exception\ContainerValueNotFoundException;

/**
 * Class GetAppointmentCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Booking\Appointment
 */
class GetAppointmentCommandHandler extends CommandHandler
{
    /**
     * @param GetAppointmentCommand $command
     *
     * @return CommandResult
     * @throws ContainerValueNotFoundException
     * @throws AccessDeniedException
     * @throws QueryExecutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function handle(GetAppointmentCommand $command)
    {
        $result = new CommandResult();

        /** @var UserApplicationService $userAS */
        $userAS = $this->container->get('application.user.service');

        /** @var AbstractCustomFieldApplicationService $customFieldService */
        $customFieldService = $this->container->get('application.customField.service');

        /** @var AppointmentApplicationService $appointmentAS */
        $appointmentAS = $this->container->get('application.booking.appointment.service');

        try {
            /** @var AbstractUser $user */
            $user = $command->getUserApplicationService()->authorization(
                $command->getPage() === 'cabinet' ? $command->getToken() : null,
                $command->getCabinetType()
            );
        } catch (AuthorizationException $e) {
            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setData(
                [
                    'reauthorize' => true
                ]
            );

            return $result;
        }

        /** @var AppointmentRepository $appointmentRepo */
        $appointmentRepo = $this->container->get('domain.booking.appointment.repository');

        /** @var ServiceRepository $serviceRepository */
        $serviceRepository = $this->container->get('domain.bookable.service.repository');

        /** @var CustomerApplicationService $customerAS */
        $customerAS = $this->container->get('application.user.customer.service');

        /** @var ProviderApplicationService $providerAS */
        $providerAS = $this->container->get('application.user.provider.service');

        /** @var CustomFieldRepository $customFieldRepository */
        $customFieldRepository = $this->container->get('domain.customField.repository');

        /** @var Appointment $appointment */
        $appointment = $appointmentRepo->getById((int)$command->getField('id'));

        // TODO: Redesign - check if could be removed, if every appointment call needs the same data returned
        $getDrawerInfo = !empty($command->getField('params')['drawer']);

        if ($userAS->isCustomer($user) && !$customerAS->hasCustomerBooking($appointment->getBookings(), $user)) {
            throw new AccessDeniedException('You are not allowed to read appointment');
        }

        /** @var PaymentRepository $paymentRepository */
        $paymentRepository = $this->container->get('domain.payment.repository');

        $bookingIds = [];

        $customerAS->removeBookingsForOtherCustomers($user, new Collection([$appointment]));

        $timeZone = !empty($command->getField('params')['timeZone'])
            ? $command->getField('params')['timeZone']
            : ($user && $user->getType() === Entities::PROVIDER ? $providerAS->getTimeZone($user) : null);

        if ($timeZone) {
            $appointment->getBookingStart()->getValue()->setTimezone(new DateTimeZone($timeZone));

            $appointment->getBookingEnd()->getValue()->setTimezone(new DateTimeZone($timeZone));
        }

        /** @var SettingsService $settingsDS */
        $settingsDS = $this->container->get('domain.settings.service');

        $badges = $settingsDS->isFeatureEnabled('employeeBadge')
            ? $settingsDS->getSetting('roles', 'providerBadges')
            : [];

        $badge = !empty($badges['badges']) && $appointment->getProvider()->getBadgeId() ?
            array_filter(
                $badges['badges'],
                function ($badge) use ($appointment) {
                    return $badge['id'] === $appointment->getProvider()->getBadgeId()->getValue();
                }
            )
            : null;

        $bookingsPrice = 0;
        $paidPrice     = 0;
        $bookedSpots   = 0;
        $bookings      = [];

        /** @var PaymentApplicationService $paymentAS */
        $paymentAS = $this->container->get('application.payment.service');

        /** @var AppointmentReservationService $reservationService */
        $reservationService = $this->container->get('application.reservation.service')->get(Entities::APPOINTMENT);

        /** @var Collection $customFieldsCollection */
        $customFieldsCollection = $customFieldRepository->getAll([], false);

        /** @var CustomerBooking $booking */
        foreach ($appointment->getBookings()->getItems() as $booking) {
            /** @var Payment $payment */
            foreach ($booking->getPayments()->getItems() as $payment) {
                if ($payment->getParentId() && $payment->getParentId()->getValue()) {
                    try {
                        /** @var Payment $parentPayment */
                        $parentPayment = $paymentRepository->getById($payment->getParentId()->getValue());

                        $bookingIds[] = $parentPayment->getCustomerBookingId()->getValue();
                    } catch (\Exception $e) {
                    }
                }

                /** @var Collection $relatedPayments */
                $relatedPayments = $paymentRepository->getByEntityId(
                    $payment->getParentId() ? $payment->getParentId()->getValue() : $payment->getId()->getValue(),
                    'parentId'
                );

                /** @var Payment $relatedPayment */
                foreach ($relatedPayments->getItems() as $relatedPayment) {
                    $bookingIds[] = $relatedPayment->getCustomerBookingId()->getValue();
                }
            }
        }

        /** @var Collection $recurringAppointments */
        $recurringAppointments = $bookingIds ? $appointmentRepo->getFiltered(
            [
                'bookingIds' => array_unique($bookingIds),
                'customerId' => !empty($command->getField('params')['customerId']) ? $command->getField('params')['customerId'] : null
            ]
        ) : $appointmentRepo->getFiltered(
            [
                'parentId' => $appointment->getParentId() ?
                    $appointment->getParentId()->getValue() : $appointment->getId()->getValue()
            ]
        );

        if ($recurringAppointments->keyExists($appointment->getId()->getValue())) {
            $recurringAppointments->deleteItem($appointment->getId()->getValue());
        }

        $appointmentArray = $appointment->toArray();
        if (!empty($appointmentArray['lessonSpace'])) {
            /** @var SettingsService $settingsDS */
            $settingsDS = $this->container->get('domain.settings.service');

            $lessonSpaceApiKey    = $settingsDS->getSetting('lessonSpace', 'apiKey');
            $lessonSpaceEnabled   = $settingsDS->getSetting('lessonSpace', 'enabled');
            $lessonSpaceCompanyId = $settingsDS->getSetting('lessonSpace', 'companyId');
            if ($lessonSpaceEnabled && $lessonSpaceApiKey && $lessonSpaceCompanyId) {
                /** @var AbstractLessonSpaceService $lessonSpaceService */
                $lessonSpaceService = $this->container->get('infrastructure.lesson.space.service');
                $spaceId            = explode("https://www.thelessonspace.com/space/", $appointmentArray['lessonSpace']);
                if ($spaceId && count($spaceId) > 1) {
                    $appointmentArray['lessonSpaceDetails'] = $lessonSpaceService->getSpace($lessonSpaceApiKey, $lessonSpaceCompanyId, $spaceId[1]);
                }
            }
        }

        if (isset($appointmentArray['notifyParticipants'])) {
            $appointmentArray['notifyParticipants'] = intval($appointmentArray['notifyParticipants']);
        }
        if (isset($appointmentArray['createPaymentLinks'])) {
            $appointmentArray['createPaymentLinks'] = intval($appointmentArray['createPaymentLinks']);
        }

        $service = $serviceRepository->getByCriteria(
            ['services' => [$appointment->getServiceId()->getValue()]]
        )->getItem($appointment->getServiceId()->getValue());

        if ($getDrawerInfo) {
            $wcTax = 0;
            $wcDiscount = 0;

            /** @var CustomerBooking $booking */
            foreach ($appointment->getBookings()->getItems() as $booking) {
                $customFields   = [];
                $bookingPrice   = $paymentAS->calculateAppointmentPrice($booking->toArray(), 'appointment');
                $bookingsPrice += $bookingPrice;

                $bookedSpots += $booking->getPersons()->getValue();

                // Create bookable with extras to properly calculate payment amount
                $bookableWithExtras = $paymentAS->createBookableWithExtras($booking->toArray(), 'appointment');
                $bookingPaymentAmount = $reservationService->getPaymentAmount($booking, $bookableWithExtras, true);

                $bookingPaidPrice = 0;
                $paymentMethods   = [];
                $wcOrderUrls      = [];
                foreach ($booking->getPayments()->toArray() as $paymentItem) {
                    $paymentMethods[] = $paymentItem['gateway'];
                    if ($paymentItem['status'] === 'paid' || $paymentItem['status'] === 'partiallyPaid') {
                        $bookingPaidPrice += $paymentItem['amount'];
                    }

                    $paymentAS->addWcFields($paymentItem);

                    $wcTax += !empty($paymentItem['wcItemTaxValue']) ? $paymentItem['wcItemTaxValue'] : 0;

                    $wcDiscount += !empty($paymentItem['wcItemCouponValue']) ? $paymentItem['wcItemCouponValue'] : 0;

                    if (!empty($paymentItem['wcOrderId'])) {
                        $wcOrderUrls[$paymentItem['wcOrderId']] = $paymentItem['wcOrderUrl'];
                    }
                }

                $paidPrice += $bookingPaidPrice;

                $customerBirthday = $booking->getCustomer() && $booking->getCustomer()->getBirthday() ?
                    $booking->getCustomer()->getBirthday()->getValue()->format('Y-m-d') :
                    null;

                if ($booking->getCustomFields() && $booking->getCustomFields()->getValue()) {
                    $customFields = $customFieldService->reformatCustomField($booking, $customFields, $customFieldsCollection);
                }

                $total = $bookingPaymentAmount['subtotal']
                    + $bookingPaymentAmount['total_tax']
                    + $wcTax
                    - $bookingPaymentAmount['discount']
                    - $bookingPaymentAmount['deduction']
                    - $wcDiscount;

                $bookings[] = [
                    'id' => $booking->getId()->getValue(),
                    'customer' => $booking->getCustomer() ? array_merge($booking->getCustomer()->toArray(), ['birthday' => $customerBirthday]) : null,
                    'status' => $booking->getStatus()->getValue(),
                    'isPackageBooking' => !!$booking->getPackageCustomerService(),
                    'payment' => [
                        'paymentMethods' => $paymentMethods,
                        'wcOrderUrls' => $wcOrderUrls,
                        'status' => $paymentAS->getFullStatus($booking->toArray(), 'appointment'),
                        'total' => $total,
                        'tax' => $bookingPaymentAmount['total_tax'],
                        'wcTax' => $wcTax,
                        'discount' => $bookingPaymentAmount['discount'] + $bookingPaymentAmount['deduction'],
                        'wcDiscount' => $wcDiscount,
                        'service' => $bookingPaymentAmount['bookable'],
                        'extras' => $bookingPaymentAmount['subtotal'] - $bookingPaymentAmount['bookable'],
                        'subtotal' => $bookingPaymentAmount['subtotal'],
                        'paid' => $bookingPaidPrice,
                        'due' => max($total - $bookingPaidPrice, 0),
                        'id' => $booking->getPayments()->length() > 0 ? $booking->getPayments()->toArray()[0]['id'] : null,
                    ],
                    'bookedSpots' => $booking->getPersons()->getValue(),
                    'customFields' => $customFields,
                    'extras' => $booking->getExtras() ? array_map(
                        function ($extra) use ($service) {
                            $serviceExtra = $service->getExtras()->getItem($extra['extraId']);
                            return array_merge(
                                $extra,
                                ['name' => $serviceExtra ? $serviceExtra->getName()->getValue() : null]
                            );
                        },
                        $booking->getExtras()->toArray()
                    ) : null,
                    'duration' => $booking->getDuration()
                        ? $booking->getDuration()->getValue()
                        : $service->getDuration()->getValue(),
                ];
            }

            $serviceSettingsRaw = $service->getSettings() ? $service->getSettings()->getValue() : null;
            $waitingListEnabled = false;
            if (!empty($serviceSettingsRaw)) {
                $serviceSettings = json_decode($serviceSettingsRaw, true);
                if (isset($serviceSettings['waitingList']['enabled'])) {
                    $waitingListEnabled = (bool)$serviceSettings['waitingList']['enabled'];
                }
            }

            $appointmentArray = [
                'id' => $appointment->getId()->getValue(),
                'employee' => $appointment->getProvider() ? [
                    'id' => $appointment->getProvider()->getId()->getValue(),
                    'firstName' => $appointment->getProvider()->getFirstName()->getValue(),
                    'lastName' => $appointment->getProvider()->getLastName() ? $appointment->getProvider()->getLastName()->getValue() : null,
                    'picture' => $appointment->getProvider()->getPicture() ? $appointment->getProvider()->getPicture()->getThumbPath() : null,
                    'badge' => !empty($badge) ? array_values($badge)[0] : null,
                ] : null,
                'location' => $appointment->getLocation() ? [
                    'id' => $appointment->getLocation()->getId()->getValue(),
                    'name' => $appointment->getLocation()->getName() ? $appointment->getLocation()->getName()->getValue() : null
                ] : null,
                'service' => $appointment->getService() ? [
                    'id' => $appointment->getService()->getId()->getValue(),
                    'name' => $appointment->getService()->getName()->getValue(),
                    'color' => $appointment->getService()->getColor() ? $appointment->getService()->getColor()->getValue() : null,
                    'pictureThumbPath' => $appointment->getService()->getPicture() ? $appointment->getService()->getPicture()->getThumbPath() : null,
                    'settings' => [
                        'waitingListEnabled' => $waitingListEnabled,
                    ],
                ] : null,
                'bookingStartDateTime' => $appointment->getBookingStart()->getValue()->format('Y-m-d H:i:s'),
                'bookingEndDateTime' => $appointment->getBookingEnd()->getValue()->format('Y-m-d H:i:s'),
                'recurringCount' => $recurringAppointments->length(),
                'googleMeetLink' => $appointment->getGoogleMeetUrl(),
                'zoomHostLink' => $appointment->getZoomMeeting() ? $appointment->getZoomMeeting()->getStartUrl()->getValue() : null,
                'zoomJoinLink' => $appointment->getZoomMeeting() ? $appointment->getZoomMeeting()->getJoinUrl()->getValue() : null,
                'lessonSpace' => $appointment->getLessonSpace() ? $appointment->getLessonSpace() : null,
                'microsoftTeamsLink' => $appointment->getMicrosoftTeamsUrl() ? $appointment->getMicrosoftTeamsUrl() : null,
                'note' => $appointment->getInternalNotes() ? $appointment->getInternalNotes()->getValue() : null,
                'status' => $appointment->getStatus()->getValue(),
                'bookings' => $bookings,
                'price' => [
                    'total' => $bookingsPrice
                ],
                'paidPrice' => $paidPrice,
                'bookedSpots' => $bookedSpots,
                'cancelable' => $appointmentAS->isCancelable($appointment, $service, $user),
                'reschedulable' => $appointmentAS->isReschedulable($appointment, $service, $user),
            ];
        }

        $appointmentArray = apply_filters('amelia_get_appointment_filter', $appointmentArray);

        do_action('amelia_get_appointment', $appointmentArray);


        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully retrieved appointment');
        $result->setData(
            [
                Entities::APPOINTMENT => $appointmentArray,
                'recurring'           => $recurringAppointments->toArray()
            ]
        );

        return $result;
    }
}
