<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Application\Services\Notification;

use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Invoice\AbstractInvoiceApplicationService;
use AmeliaBooking\Application\Services\Payment\PaymentApplicationService;
use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Bookable\Service\Service;
use AmeliaBooking\Domain\Entity\Booking\Appointment\Appointment;
use AmeliaBooking\Domain\Entity\Booking\Appointment\CustomerBooking;
use AmeliaBooking\Domain\Entity\Booking\Event\Event;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Entity\Notification\Notification;
use AmeliaBooking\Domain\Entity\Payment\Payment;
use AmeliaBooking\Domain\Entity\User\Customer;
use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Domain\ValueObjects\String\BookingStatus;
use AmeliaBooking\Domain\ValueObjects\String\NotificationStatus;
use AmeliaBooking\Infrastructure\Common\Container;
use AmeliaBooking\Infrastructure\Common\Exceptions\NotFoundException;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Bookable\Service\ServiceRepository;
use AmeliaBooking\Infrastructure\Repository\User\CustomerRepository;

/**
 * Class AppointmentNotificationService
 *
 * @package AmeliaBooking\Application\Services\Notification
 */
class AppointmentNotificationService
{
    protected $container;

    /**
     * AppointmentNotificationService constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param AbstractNotificationService $notificationService
     * @param Appointment                 $appointment
     * @param bool                        $logNotification
     *
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     */
    public function sendProviderStatusNotifications(
        $notificationService,
        $appointment,
        $logNotification = true
    ) {
        /** @var Collection $providerNotifications */
        $providerNotifications = $notificationService->getByNameAndType(
            "provider_appointment_{$appointment->getStatus()->getValue()}",
            $notificationService->getType()
        );

        $sendDefault = $notificationService->sendDefault($providerNotifications, $appointment->toArray());

        /** @var Notification $providerNotification */
        foreach ($providerNotifications->getItems() as $providerNotification) {
            if (
                $providerNotification->getStatus()->getValue() === NotificationStatus::ENABLED &&
                $notificationService->checkCustom($providerNotification, $appointment->toArray(), $sendDefault)
            ) {
                $notificationService->sendNotification(
                    $appointment->toArray(),
                    $providerNotification,
                    $logNotification
                );
            }
        }
    }

    /**
     * @param AbstractNotificationService $notificationService
     * @param Appointment                 $appointment
     * @param Collection                  $bookings
     * @param bool                        $logNotification
     * @param bool                        $isBackend
     * @param bool                        $sendInvoice
     *
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     * @throws NotFoundException
     * @throws AccessDeniedException
     */
    public function sendCustomersStatusNotifications(
        $notificationService,
        $appointment,
        $bookings,
        $logNotification = true,
        $isBackend = true,
        $sendInvoice = false,
        $notifyCustomers = true
    ) {
        /** @var AbstractInvoiceApplicationService $invoiceService */
        $invoiceService = $this->container->get('application.invoice.service');

        /** @var Collection $statusNotifications */
        $statusNotifications = new Collection();

        /** @var CustomerBooking $booking */
        foreach ($bookings->getItems() as $bookingKey => $booking) {
            if ($booking->isChangedStatus() && $booking->isChangedStatus()->getValue()) {
                $notificationStatus = $appointment->getStatus()->getValue() === BookingStatus::PENDING &&
                (
                    $booking->getStatus()->getValue() === BookingStatus::APPROVED ||
                    $booking->getStatus()->getValue() === BookingStatus::PENDING
                )
                    ? BookingStatus::PENDING
                    : $booking->getStatus()->getValue();

                if (!$statusNotifications->keyExists($notificationStatus)) {
                    $statusNotifications->addItem(
                        $notificationService->getByNameAndType(
                            "customer_appointment_{$notificationStatus}",
                            $notificationService->getType()
                        ),
                        $notificationStatus
                    );
                }

                /** @var Collection $customerNotifications */
                $customerNotifications = $statusNotifications->getItem($notificationStatus);

                $sendDefault = $notificationService->sendDefault($customerNotifications, $appointment->toArray());

                /** @var Notification $customerNotification */
                foreach ($customerNotifications->getItems() as $customerNotification) {
                    if (
                        $customerNotification->getStatus()->getValue() === NotificationStatus::ENABLED &&
                        $notificationService->checkCustom(
                            $customerNotification,
                            $appointment->toArray(),
                            $sendDefault
                        ) && $notifyCustomers
                    ) {
                        if (
                            $customerNotification->getContent() &&
                            $customerNotification->getContent()->getValue() &&
                            strpos($customerNotification->getContent()->getValue(), '%payment_link_') !== false
                        ) {
                            $this->setPaymentLink($appointment, $bookingKey);
                        }

                        $notificationService->sendNotification(
                            array_merge(
                                $appointment->toArray(),
                                [
                                    'bookings'  => $bookings->toArray(),
                                    'isBackend' => $isBackend,
                                ]
                            ),
                            $customerNotification,
                            $logNotification,
                            $bookingKey,
                            null,
                            (
                                $sendInvoice &&
                                $booking->getPayments()->length() &&
                                $booking->getPayments()->keyExists(0) &&
                                $booking->getStatus()->getValue() !== BookingStatus::WAITING
                            )
                            ? $invoiceService->generateInvoice(
                                $booking->getPayments()->getItem(0)->getId()->getValue()
                            )
                            : null
                        );
                    }
                }
            }
        }
    }

    /**
     * @param AbstractNotificationService $notificationService
     * @param Appointment                 $appointment
     * @param bool                        $notifyProvider
     * @param bool                        $notifyCustomers
     *
     * @throws QueryExecutionException
     * @throws InvalidArgumentException
     */
    public function sendRescheduledNotifications(
        $notificationService,
        $appointment,
        $notifyProvider = true,
        $notifyCustomers = true
    ) {
        if ($notifyProvider) {
            /** @var Collection $providerNotifications */
            $providerNotifications = $notificationService->getByNameAndType(
                "provider_appointment_rescheduled",
                $notificationService->getType()
            );

            $sendDefault = $notificationService->sendDefault($providerNotifications, $appointment->toArray());

            /** @var Notification $providerNotification */
            foreach ($providerNotifications->getItems() as $providerNotification) {
                if (
                    $providerNotification->getStatus()->getValue() === NotificationStatus::ENABLED &&
                    $notificationService->checkCustom($providerNotification, $appointment->toArray(), $sendDefault)
                ) {
                    $notificationService->sendNotification(
                        $appointment->toArray(),
                        $providerNotification,
                        true
                    );
                }
            }
        }

        if ($notifyCustomers && $appointment->isNotifyParticipants()) {
            /** @var Collection $customerNotifications */
            $customerNotifications = $notificationService->getByNameAndType(
                "customer_appointment_rescheduled",
                $notificationService->getType()
            );

            $sendDefault = $notificationService->sendDefault($customerNotifications, $appointment->toArray());

            /** @var Notification $customerNotification */
            foreach ($customerNotifications->getItems() as $customerNotification) {
                if (
                    $customerNotification->getStatus()->getValue() === NotificationStatus::ENABLED &&
                    $notificationService->checkCustom($customerNotification, $appointment->toArray(), $sendDefault)
                ) {
                    /** @var CustomerBooking $booking */
                    foreach ($appointment->getBookings()->getItems() as $bookingKey => $booking) {
                        if (
                            (!$booking->isNew() || !$booking->isNew()->getValue()) &&
                            (
                                $booking->getStatus()->getValue() === BookingStatus::APPROVED ||
                                $booking->getStatus()->getValue() === BookingStatus::PENDING
                            )
                        ) {
                            $notificationService->sendNotification(
                                $appointment->toArray(),
                                $customerNotification,
                                true,
                                $bookingKey
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * Send waiting list available spot notifications.
     * Triggered when a booking for an approved appointment is canceled and there are waiting bookings.
     * Sends one notification to provider and one to each waiting customer.
     *
     * @param AbstractNotificationService $notificationService
     * @param Appointment $appointment
     * @param Collection $waitingBookings Collection of CustomerBooking objects with status 'waiting'
     *
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     */
    public function sendWaitingListAvailableSpotNotification(
        $notificationService,
        $appointment,
        $waitingBookings
    ) {
        if (!$waitingBookings->length()) {
            return;
        }

        // Provider notification (single)
        /** @var Collection $providerNotifications */
        $providerNotifications = $notificationService->getByNameAndType(
            'provider_appointment_waiting_available_spot',
            $notificationService->getType()
        );
        $sendDefaultProvider = $notificationService->sendDefault($providerNotifications, $appointment->toArray());
        foreach ($providerNotifications->getItems() as $providerNotification) {
            if (
                $providerNotification->getStatus()->getValue() === NotificationStatus::ENABLED &&
                $notificationService->checkCustom($providerNotification, $appointment->toArray(), $sendDefaultProvider)
            ) {
                $notificationService->sendNotification(
                    $appointment->toArray(),
                    $providerNotification,
                    true
                );
            }
        }

        // Customer notifications (each waiting booking)
        /** @var Collection $customerNotifications */
        $customerNotifications = $notificationService->getByNameAndType(
            'customer_appointment_waiting_available_spot',
            $notificationService->getType()
        );
        $sendDefaultCustomer = $notificationService->sendDefault($customerNotifications, $appointment->toArray());
        foreach ($customerNotifications->getItems() as $customerNotification) {
            if (
                $customerNotification->getStatus()->getValue() === NotificationStatus::ENABLED &&
                $notificationService->checkCustom($customerNotification, $appointment->toArray(), $sendDefaultCustomer)
            ) {
                foreach ($waitingBookings->getItems() as $waitingBooking) {
                    $bookingKey = null;
                    foreach ($appointment->getBookings()->getItems() as $appBookingKey => $appBooking) {
                        if ($appBooking->getId()->getValue() === $waitingBooking->getId()->getValue()) {
                            $bookingKey = $appBookingKey;
                            break;
                        }
                    }
                    if ($bookingKey !== null) {
                        $notificationService->sendNotification(
                            array_merge($appointment->toArray(), [
                                'bookings' => $appointment->getBookings()->toArray(),
                            ]),
                            $customerNotification,
                            true,
                            $bookingKey
                        );
                    }
                }
            }
        }
    }

    /**
     * @param AbstractNotificationService $notificationService
     * @param Appointment                 $appointment
     * @param bool                        $notifyProvider
     * @param bool                        $notifyCustomers
     *
     * @throws QueryExecutionException
     * @throws InvalidArgumentException
     */
    public function sendUpdatedNotifications(
        $notificationService,
        $appointment,
        $notifyProvider,
        $notifyCustomers
    ) {
        if ($notifyProvider) {
            /** @var Collection $providerNotifications */
            $providerNotifications = $notificationService->getByNameAndType(
                "provider_appointment_updated",
                $notificationService->getType()
            );

            $sendDefault = $notificationService->sendDefault($providerNotifications, $appointment->toArray());

            /** @var Notification $providerNotification */
            foreach ($providerNotifications->getItems() as $providerNotification) {
                if (
                    $providerNotification->getStatus()->getValue() === NotificationStatus::ENABLED &&
                    $notificationService->checkCustom($providerNotification, $appointment->toArray(), $sendDefault)
                ) {
                    $appointmentArray = $appointment->toArray();
                    $appointmentArray['sendForAllBookings'] = true;

                    $notificationService->sendNotification(
                        $appointmentArray,
                        $providerNotification,
                        true
                    );
                }
            }
        }

        if ($notifyCustomers && $appointment->isNotifyParticipants()) {
            /** @var Collection $customerNotifications */
            $customerNotifications = $notificationService->getByNameAndType(
                "customer_appointment_updated",
                $notificationService->getType()
            );

            $sendDefault = $notificationService->sendDefault($customerNotifications, $appointment->toArray());

            /** @var Notification $customerNotification */
            foreach ($customerNotifications->getItems() as $customerNotification) {
                if (
                    $customerNotification->getStatus()->getValue() === NotificationStatus::ENABLED &&
                    $notificationService->checkCustom($customerNotification, $appointment->toArray(), $sendDefault)
                ) {
                    /** @var CustomerBooking $booking */
                    foreach ($appointment->getBookings()->getItems() as $bookingKey => $booking) {
                        if (
                            $booking->getStatus()->getValue() === BookingStatus::APPROVED &&
                            (!$booking->isNew() || !$booking->isNew()->getValue()) &&
                            $booking->isUpdated() &&
                            $booking->isUpdated()->getValue()
                        ) {
                            $notificationService->sendNotification(
                                $appointment->toArray(),
                                $customerNotification,
                                true,
                                $bookingKey
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * @param AbstractNotificationService $notificationService
     * @param Event                       $event
     * @param bool                        $logNotification
     * @param int                         $bookingKey
     *
     *
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     */
    public function sendQrNotifications(
        $notificationService,
        $event,
        $bookingKey,
        $logNotification = true
    ) {
        $notifications = $notificationService->getByNameAndType(
            "customer_event_qr_code",
            $notificationService->getType()
        );

        $qrNotification = $notifications->getItem($notifications->keys()[0]);

        if ($qrNotification->getStatus()->getValue() === NotificationStatus::ENABLED) {
            $notificationService->sendNotification(
                $event->toArray(),
                $qrNotification,
                $logNotification,
                $bookingKey
            );
        }
    }

    /**
     * @param Appointment  $appointment
     * @param int          $bookingKey
     *
     *
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     * @throws NotFoundException
     */
    private function setPaymentLink($appointment, $bookingKey)
    {
        /** @var CustomerBooking $booking */
        $booking = $appointment->getBookings()->getItem($bookingKey);

        /** @var Payment $payment */
        $payment = $booking->getPayments() && $booking->getPayments()->keyExists(0)
            ? $booking->getPayments()->getItem(0)
            : null;

        if ($payment && $payment->getId() && !$payment->getPaymentLinks()) {
            /** @var PaymentApplicationService $paymentAS */
            $paymentAS = $this->container->get('application.payment.service');

            /** @var ServiceRepository $serviceRepository */
            $serviceRepository = $this->container->get('domain.bookable.service.repository');

            /** @var CustomerRepository $customerRepository */
            $customerRepository = $this->container->get('domain.users.customers.repository');

            /** @var Service $service */
            $service = $appointment->getService() ?: $serviceRepository->getById($appointment->getServiceId()->getValue());

            /** @var Customer $customer */
            $customer = $booking->getCustomer() ?: $customerRepository->getById($booking->getCustomerId()->getValue());

            $payment->setPaymentLinks(
                $paymentAS->createPaymentLink(
                    [
                        'type'        => Entities::APPOINTMENT,
                        'booking'     => $booking->toArray(),
                        'appointment' => $appointment->toArray(),
                        'paymentId'   => $payment->getId()->getValue(),
                        'bookable'    => $service->toArray(),
                        'customer'    => $customer->toArray(),
                    ],
                    $bookingKey
                )
            );
        }
    }
}
