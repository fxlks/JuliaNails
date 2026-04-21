<?php

namespace AmeliaBooking\Application\Commands\Booking\Event;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Booking\EventApplicationService;
use AmeliaBooking\Application\Services\CustomField\AbstractCustomFieldApplicationService;
use AmeliaBooking\Application\Services\Payment\PaymentApplicationService;
use AmeliaBooking\Application\Services\User\ProviderApplicationService;
use AmeliaBooking\Application\Services\User\UserApplicationService;
use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Common\Exceptions\AuthorizationException;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Booking\Appointment\CustomerBooking;
use AmeliaBooking\Domain\Entity\Booking\Event\CustomerBookingEventTicket;
use AmeliaBooking\Domain\Entity\Booking\Event\Event;
use AmeliaBooking\Domain\Entity\Booking\Event\EventTicket;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Entity\User\AbstractUser;
use AmeliaBooking\Domain\Entity\User\Provider;
use AmeliaBooking\Domain\Factory\Booking\Appointment\CustomerBookingFactory;
use AmeliaBooking\Domain\Factory\Booking\Event\EventFactory;
use AmeliaBooking\Domain\Services\DateTime\DateTimeService;
use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Domain\ValueObjects\String\BookableType;
use AmeliaBooking\Domain\ValueObjects\String\BookingStatus;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Booking\Appointment\CustomerBookingRepository;
use AmeliaBooking\Infrastructure\Repository\CustomField\CustomFieldRepository;
use Exception;

/**
 * Class GetEventBookingsCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Booking\Event
 */
class GetEventBookingsCommandHandler extends CommandHandler
{
    /**
     * @param GetEventBookingsCommand $command
     *
     * @return CommandResult
     *
     * @throws AccessDeniedException
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     * @throws Exception
     */
    public function handle(GetEventBookingsCommand $command)
    {
        $result = new CommandResult();

        /** @var SettingsService $settingsDS */
        $settingsDS = $this->container->get('domain.settings.service');
        /** @var UserApplicationService $userAS */
        $userAS = $this->container->get('application.user.service');
        /** @var PaymentApplicationService $paymentAS */
        $paymentAS = $this->container->get('application.payment.service');
        /** @var EventApplicationService $eventApplicationService */
        $eventApplicationService = $this->container->get('application.booking.event.service');
        /** @var CustomerBookingRepository $bookingRepository */
        $bookingRepository = $this->container->get('domain.booking.customerBooking.repository');
        /** @var CustomFieldRepository $customFieldRepository */
        $customFieldRepository = $this->container->get('domain.customField.repository');
        /** @var ProviderApplicationService $providerAS */
        $providerAS = $this->container->get('application.user.provider.service');
        /** @var AbstractCustomFieldApplicationService $customFieldService */
        $customFieldService = $this->container->get('application.customField.service');

        $params = $command->getField('params');

        if (isset($params['dates']) && empty($params['dates'][0]) && empty($params['dates'][1])) {
            unset($params['dates']);
        }

        $isCabinetPage = $command->getPage() === 'cabinet';

        try {
            /** @var AbstractUser $user */
            $user = $command->getUserApplicationService()->authorization(
                $isCabinetPage ? $command->getToken() : null,
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

        if ($user && $userAS->isAmeliaUser($user) && $userAS->isCustomer($user)) {
            $params['customers'] = [$user->getId()->getValue()];
        }

        if ($user && $user->getType() === AbstractUser::USER_ROLE_PROVIDER) {
            $params['providers'] = [$user->getId()->getValue()];
        }

        $providerTimeZoneSet = ($user instanceof Provider) && $user->getTimeZone() && $user->getTimeZone()->getValue();

        if (!empty($params['dates'])) {
            if (!empty($params['dates'][0])) {
                $params['dates'][0] .= ' 00:00:00';
            }
            if (!empty($params['dates'][1])) {
                $params['dates'][1] .= ' 23:59:59';
            }
        }

        $attendeeCount = 0;

        $waitingCount = 0;

        $maxCapacity = 0;

        $waitingCapacity = 0;

        if ($isCabinetPage) {
            /** @var Event $event */
            $event = $eventApplicationService->getEventById(
                (int)$params['events'][0],
                [
                    'fetchEventsTickets'    => true,
                    'fetchBookings'         => true,
                    'fetchBookingsTickets'  => true,
                ]
            );


            if ($event->getCustomPricing()->getValue()) {
                /** @var CustomerBooking $customerBooking */
                foreach ($event->getBookings()->getItems() as $customerBooking) {
                    if (
                        $customerBooking->getStatus()->getValue() === BookingStatus::APPROVED ||
                        $customerBooking->getStatus()->getValue() === BookingStatus::PENDING
                    ) {
                        /** @var CustomerBookingEventTicket $bookingToEventTicket */
                        foreach ($customerBooking->getTicketsBooking()->getItems() as $bookingToEventTicket) {
                            $attendeeCount += $bookingToEventTicket->getPersons()->getValue();
                        }
                    }

                    if ($customerBooking->getStatus()->getValue() === BookingStatus::WAITING) {
                        /** @var CustomerBookingEventTicket $bookingToEventTicket */
                        foreach ($customerBooking->getTicketsBooking()->getItems() as $bookingToEventTicket) {
                            $waitingCount += $bookingToEventTicket->getPersons()->getValue();
                        }
                    }
                }

                /** @var EventTicket $ticket */
                foreach ($event->getCustomTickets()->getItems() as $ticket) {
                    $maxCapacity += $ticket->getSpots()->getValue();
                }
            } else {
                $maxCapacity = $event->getMaxCapacity()->getValue();

                /** @var CustomerBooking $customerBooking */
                foreach ($event->getBookings()->getItems() as $customerBooking) {
                    if (
                        $customerBooking->getStatus()->getValue() === BookingStatus::APPROVED ||
                        $customerBooking->getStatus()->getValue() === BookingStatus::PENDING
                    ) {
                        $attendeeCount += $customerBooking->getPersons()->getValue();
                    }
                }

                /** @var CustomerBooking $customerBooking */
                foreach ($event->getBookings()->getItems() as $customerBooking) {
                    if ($customerBooking->getStatus()->getValue() === BookingStatus::WAITING) {
                        $waitingCount += $customerBooking->getPersons()->getValue();
                    }
                }
            }

            $eventSettings = $settingsDS->isFeatureEnabled('waitingList') && $event->getSettings() && $event->getSettings()->getValue()
                ? json_decode($event->getSettings()->getValue(), true)
                : null;


            if ($eventSettings && !empty($eventSettings['waitingList']['enabled'])) {
                if ($event->getCustomPricing()->getValue()) {
                    /** @var EventTicket $ticket */
                    foreach ($event->getCustomTickets()->getItems() as $ticket) {
                        $waitingCapacity += $ticket->getWaitingListSpots()->getValue();
                    }
                } else {
                    $waitingCapacity = $eventSettings['waitingList']['maxCapacity'];
                }
            }
        }

        $bookingIds = $bookingRepository->getEventBookingIdsByCriteria($params, !empty($params['limit']) ? $params['limit'] : 10);

        if (!$bookingIds && $params['page'] && (int)$params['page'] > 1) {
            $params['page'] = 1;

            $bookingIds = $bookingRepository->getEventBookingIdsByCriteria($params, !empty($params['limit']) ? $params['limit'] : 10);
        }

        if (empty($bookingIds)) {
            $result->setResult(CommandResult::RESULT_SUCCESS);
            $result->setMessage('Successfully retrieved event bookings');
            $result->setData(
                [
                    Entities::BOOKINGS => [],
                    'totalCount'       => sizeof($bookingRepository->getEventBookingIdsByCriteria()),
                    'filteredCount'    => 0,
                    'attendeeCount'    => 0,
                    'waitingCount'     => 0,
                    'waitingCapacity'  => $waitingCapacity,
                    'maxCapacity'      => $maxCapacity,
                ]
            );

            return $result;
        }

        $bookings = $bookingRepository->getEventBookingsByIds(
            $bookingIds,
            array_merge(
                !empty($params['sort']) ? ['sort' => $params['sort']] : [],
                !empty($params['dates']) ? ['dates' => $params['dates']] : [],
                [
                    'fetchBookingsPayments' => true,
                    'fetchBookingsCoupons' => true,
                    'fetchProviders' => true,
                    'fetchCustomers' => true,
                    'fetchEvent' => true,
                ]
            )
        );


        /** @var Collection $customFieldsCollection */
        $customFieldsCollection = $customFieldRepository->getAll([], false);

        $customersNoShowCountIds = [];

        $noShowTagEnabled = $settingsDS->isFeatureEnabled('noShowTag');

        $eventBookings = [];

        foreach ($bookings as &$booking) {
            $customFields = [];

            ksort($booking['payments']);

            if ($noShowTagEnabled) {
                $customersNoShowCountIds[] = $booking['customer']['id'];
            }

            foreach ($booking['event']['periods'] as &$period) {
                $period['periodStart'] = DateTimeService::getCustomDateTimeFromUtc($period['periodStart']);
                $period['periodEnd']   = DateTimeService::getCustomDateTimeFromUtc($period['periodEnd']);
                if ($providerTimeZoneSet) {
                    $period['periodStart'] =
                        DateTimeService::getCustomDateTimeObjectInTimeZone($period['periodStart'], $user->getTimeZone()->getValue())->format('Y-m-d H:i:s');
                    $period['periodEnd']   =
                        DateTimeService::getCustomDateTimeObjectInTimeZone($period['periodEnd'], $user->getTimeZone()->getValue())->format('Y-m-d H:i:s');
                }
            }

            $persons = $booking['persons'];
            if (!empty($booking['event']['customPricing']) && !empty($booking['ticketsData'])) {
                /** @var CustomerBookingEventTicket $bookedTicket */
                foreach ($booking['ticketsData'] as $bookedTicket) {
                    $persons += $bookedTicket['persons'];
                }
            }

            if ($booking['tax']) {
                $booking['tax'] = json_decode($booking['tax'], true);
            }

            $booking['ticketsData'] = !empty($booking['ticketsData']) ? $booking['ticketsData'] : [];

            if (!empty($booking['event']['providers'])) {
                foreach ($booking['event']['providers'] as &$provider) {
                    if (!empty($provider['badgeId'])) {
                        $provider['badge'] = $providerAS->getBadge($provider['badgeId']);
                    }
                }
            }

            if (!empty($booking['event']['organizer']) && !empty($booking['event']['organizer']['badgeId'])) {
                $booking['event']['organizer']['badge'] = $providerAS->getBadge($booking['event']['organizer']['badgeId']);
            }

            $wcTax = 0;
            $wcDiscount = 0;
            $paid = 0;

            foreach ($booking['payments'] as $payment) {
                $paymentAS->addWcFields($payment);

                $wcTax += !empty($payment['wcItemTaxValue']) ? $payment['wcItemTaxValue'] : 0;

                $wcDiscount += !empty($payment['wcItemCouponValue']) ? $payment['wcItemCouponValue'] : 0;

                $paid = $paid + $payment['amount'];
            }

            $customFields = $customFieldService->reformatCustomField(
                CustomerBookingFactory::create($booking),
                $customFields,
                $customFieldsCollection
            );

            $eventBooking = [
                'id' => $booking['id'],
                'bookedSpots' => $persons,
                'status' => $booking['event']['status'] === 'canceled' || $booking['event']['status'] === 'rejected' ? 'canceled' : $booking['status'],
                'persons' => $persons,
                'checked' => false,
                'customer' => [
                    'id' => $booking['customer']['id'],
                    'firstName' => $booking['customer']['firstName'],
                    'lastName' => $booking['customer']['lastName'],
                    'phone' => $booking['customer']['phone'],
                    'email' => $booking['customer']['email'],
                    'note' => $booking['customer']['note']
                ],
                'code' => !empty($booking['token']) ? substr($booking['token'], 0, 5) : '',
                'event' => [
                    'id' => (int)$booking['event']['id'],
                    'name' => $booking['event']['name'],
                    'startDate' => explode(' ', array_values($booking['event']['periods'])[0]['periodStart'])[0],
                    'endDate' => explode(' ', array_values($booking['event']['periods'])[sizeof($booking['event']['periods']) - 1]['periodEnd'])[0],
                    'startTime' => explode(' ', array_values($booking['event']['periods'])[0]['periodStart'])[1],
                    'organizer' => !empty($booking['event']['organizer']) ? $booking['event']['organizer'] : null,
                    'staff' => !empty($booking['event']['providers']) ? array_values($booking['event']['providers']) : [],
                    'isZoom' => !empty(array_values($booking['event']['periods'])[0]['zoomMeeting']),
                    'isGoogleMeet' => !empty(array_values($booking['event']['periods'])[0]['googleMeetUrl']),
                    'isMicrosoftTeams' => !empty(array_values($booking['event']['periods'])[0]['microsoftTeamsUrl']),
                    'isLessonSpace' => !empty(array_values($booking['event']['periods'])[0]['lessonSpace']),
                    'isWaitingList' => !empty($booking['event']['settings']) ?
                        json_decode($booking['event']['settings'], true)['waitingList']['enabled'] :
                        false,
                ],
                'ticketsData' => $booking['ticketsData'],
                'tax' => $booking['tax'],
                'price' => $booking['price'],
                'aggregatedPrice' => $booking['aggregatedPrice'],
                'coupon' => !empty($booking['coupon']) ? $booking['coupon'] : null,
                'payment' => [
                    'status' => $paymentAS->getFullStatus($booking, BookableType::EVENT),
                    'total'  => $paymentAS->calculateAppointmentPrice($booking, BookableType::EVENT) + $wcTax - $wcDiscount,
                    'paid'   => $paid,
                ],
                'customFields' => $customFields,
                'payments' => array_values($booking['payments']),
                'qrCodes' => !empty($booking['qrCodes']) ? $booking['qrCodes'] : null,
                'cancelable' => $eventApplicationService->isCancelable(EventFactory::create($booking['event']), $user),
                'created' => !empty($booking['created']) ? explode(' ', $booking['created'])[0] : null,
            ];

            if ($isCabinetPage) {
                $eventBooking['customFields'] = $booking['customFields'];
            }

            $eventBookings[] = $eventBooking;
        }


        if ($noShowTagEnabled && !empty($customersNoShowCountIds)) {
            /** @var CustomerBookingRepository $bookingRepository */
            $bookingRepository = $this->container->get('domain.booking.customerBooking.repository');

            $customersNoShowCount = $bookingRepository->countByNoShowStatus($customersNoShowCountIds);

            foreach ($eventBookings as &$eventBooking) {
                if (!empty($customersNoShowCount[$eventBooking['customer']['id']])) {
                    $eventBooking['customer']['noShowCount'] = $customersNoShowCount[$eventBooking['customer']['id']]['count'];
                }
            }
        }

        $eventBookings = apply_filters('amelia_get_event_bookings_filter', $eventBookings);

        do_action('amelia_get_event_bookings', $eventBookings);

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully retrieved event bookings');
        $result->setData(
            [
                Entities::BOOKINGS => $eventBookings,
                'totalCount'       => sizeof($bookingRepository->getEventBookingIdsByCriteria()),
                'filteredCount'    => sizeof(
                    $bookingRepository->getEventBookingIdsByCriteria($params)
                ),
                'attendeeCount'    => $attendeeCount,
                'waitingCount'     => $waitingCount,
                'waitingCapacity'  => $waitingCapacity,
                'maxCapacity'      => $maxCapacity,
            ]
        );

        return $result;
    }
}
