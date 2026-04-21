<?php

namespace AmeliaBooking\Application\Commands\Booking\Event;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Booking\EventApplicationService;
use AmeliaBooking\Application\Services\CustomField\AbstractCustomFieldApplicationService;
use AmeliaBooking\Application\Services\Payment\PaymentApplicationService;
use AmeliaBooking\Application\Services\Reservation\EventReservationService;
use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Common\Exceptions\AuthorizationException;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Booking\Event\CustomerBookingEventTicket;
use AmeliaBooking\Domain\Entity\Booking\Event\Event;
use AmeliaBooking\Domain\Entity\Booking\Event\EventTicket;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Entity\User\AbstractUser;
use AmeliaBooking\Domain\Entity\User\Provider;
use AmeliaBooking\Domain\Factory\Booking\Appointment\CustomerBookingFactory;
use AmeliaBooking\Domain\Services\DateTime\DateTimeService;
use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Domain\ValueObjects\String\BookableType;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Booking\Appointment\CustomerBookingRepository;
use AmeliaBooking\Infrastructure\Repository\Booking\Event\EventRepository;
use AmeliaBooking\Infrastructure\Repository\CustomField\CustomFieldRepository;
use Exception;

/**
 * Class GetEventBookingCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Booking\Event
 */
class GetEventBookingCommandHandler extends CommandHandler
{
    /**
     * @param GetEventBookingCommand $command
     *
     * @return CommandResult
     *
     * @throws AccessDeniedException
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     * @throws Exception
     */
    public function handle(GetEventBookingCommand $command)
    {
        $result = new CommandResult();

        /** @var SettingsService $settingsDS */
        $settingsDS = $this->container->get('domain.settings.service');
        /** @var PaymentApplicationService $paymentAS */
        $paymentAS = $this->container->get('application.payment.service');
        /** @var CustomerBookingRepository $bookingRepository */
        $bookingRepository = $this->container->get('domain.booking.customerBooking.repository');
        /** @var EventApplicationService $eventApplicationService */
        $eventApplicationService = $this->container->get('application.booking.event.service');
        /** @var EventRepository $eventRepository */
        $eventRepository = $this->container->get('domain.booking.event.repository');
        /** @var AbstractCustomFieldApplicationService $customFieldService */
        $customFieldService = $this->container->get('application.customField.service');
        /** @var EventReservationService $reservationService */
        $reservationService = $this->container->get('application.reservation.service')->get(Entities::EVENT);
        /** @var CustomFieldRepository $customFieldRepository */
        $customFieldRepository = $this->container->get('domain.customField.repository');

        try {
            /** @var AbstractUser $user */
            $user = $command->getUserApplicationService()->authorization(
                null,
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

        $providerTimeZoneSet = $user && $user instanceof Provider && $user->getTimeZone() && $user->getTimeZone()->getValue();

        $bookings = $bookingRepository->getEventBookingsByIds(
            [$command->getArg('id')],
            array_merge(
                [
                    'fetchBookingsPayments' => true,
                    'fetchBookingsCoupons' => true,
                    'fetchCustomers' => true
                ]
            )
        );

        $eventId = $command->getField('params')['eventId'];

        /** @var Event $event */
        $event = $eventApplicationService->getEventById(
            $eventId,
            [
                'fetchEventsPeriods'    => true,
                'fetchEventsTickets'    => true,
                'fetchEventsTags'       => false,
                'fetchEventsProviders'  => false,
                'fetchEventsImages'     => true,
                'fetchBookings'         => true,
                'fetchBookingsTickets'  => true,
                'fetchBookingsUsers'    => true,
                'fetchBookingsPayments' => true,
                'fetchBookingsCoupons'  => true,
                'fetchEventsOrganizer'  => true,
                'fetchEventsLocation'   => true,
            ]
        );

        if (empty($bookings) || !$event) {
            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setMessage('Could not retrieve event booking');
            $result->setData(
                [
                    Entities::BOOKING => []
                ]
            );

            return $result;
        }

        /** @var Collection $customFieldsCollection */
        $customFieldsCollection = $customFieldRepository->getAll([], false);

        $booking = array_values($bookings)[0];

        if ($user && $user->getType() === Entities::CUSTOMER && $booking['customerId'] !== $user->getId()->getValue()) {
            throw new AccessDeniedException('You are not allowed to read event booking');
        }

        $customersNoShowCountIds = [];

        $noShowTagEnabled = $settingsDS->isFeatureEnabled('noShowTag');

        if ($noShowTagEnabled) {
            $customersNoShowCountIds[] = $booking['customer']['id'];
        }

        $eventInfo = $eventApplicationService->getEventInfo($event);

        $eventArray = $event->toArray();

        foreach ($eventArray['periods'] as &$period) {
            $period['periodStart'] = DateTimeService::getCustomDateTimeFromUtc($period['periodStart']);
            $period['periodEnd']   = DateTimeService::getCustomDateTimeFromUtc($period['periodEnd']);
            if ($providerTimeZoneSet) {
                $period['periodStart'] =
                    DateTimeService::getCustomDateTimeObjectInTimeZone($period['periodStart'], $user->getTimeZone()->getValue())->format('Y-m-d H:i:s');
                $period['periodEnd']   =
                    DateTimeService::getCustomDateTimeObjectInTimeZone($period['periodEnd'], $user->getTimeZone()->getValue())->format('Y-m-d H:i:s');
            }
        }

        $eventStartDateTime = array_values($event->getPeriods()->toArray())[0]['periodStart'];

        $eventEndDateTime = array_values($event->getPeriods()->toArray())[$event->getPeriods()->length() - 1]['periodEnd'];

        $recurringEvents = [];
        if ($event->getRecurring()) {
            $recurringEvents =
                $eventRepository->getFilteredIds(['parentId' => $event->getParentId() ?
                    $event->getParentId()->getValue() :
                    $event->getId()->getValue(), 'dates' => [$eventStartDateTime]], null);
        }

        usort(
            $eventArray['gallery'],
            function ($picture1, $picture2) {
                return $picture1['position'] <=> $picture2['position'];
            }
        );

        $eventArray = array_merge(
            $eventInfo,
            [
                'id' => $eventArray['id'],
                'name' => $eventArray['name'],
                'show' => $eventArray['show'],
                'pictureThumbPath' => !empty($eventArray['gallery'][0]) ? $eventArray['gallery'][0]['pictureThumbPath'] : null,
                'customPricing' => $eventArray['customPricing'],
                'startDate' => explode(' ', $eventStartDateTime)[0],
                'endDate' => explode(' ', $eventEndDateTime)[0],
                'isZoom' => !empty($eventArray['periods'][0]['zoomMeeting']),
                'isGoogleMeet' => !empty($eventArray['periods'][0]['googleMeetUrl']),
                'isWaitingList' => !empty($eventArray['settings']) ? json_decode($eventArray['settings'], true)['waitingList']['enabled'] : false,
                'recurringCount' => sizeof($recurringEvents) > 0 ? (sizeof($recurringEvents) - 1) : 0,
                'location' =>
                $event->getCustomLocation() ?
                    ['name' => $event->getCustomLocation()->getValue()] : ($event->getLocationId() ? $event->getLocation()->toArray() : null),
                'periods' => array_map(function ($period) {
                    return [
                        'periodStart' => $period['periodStart'],
                        'periodEnd' => $period['periodEnd']
                    ];
                }, $event->getPeriods()->toArray()),
            ]
        );

        $eventArray['organizer'] = $event->getOrganizerId() && $event->getOrganizer() ? [
            'id' =>  $event->getOrganizerId(),
            'firstName' =>  $event->getOrganizer()->getFirstName()->getValue(),
            'lastName' => $event->getOrganizer()->getLastName() ? $event->getOrganizer()->getLastName()->getValue() : null,
            'picture' => $event->getOrganizer()->getPicture() ? $event->getOrganizer()->getPicture()->getThumbPath() : null,
        ] : null;


        $persons = $booking['persons'];
        if (!empty($eventArray['customPricing']) && !empty($booking['ticketsData'])) {
            /** @var CustomerBookingEventTicket $bookedTicket */
            foreach ($booking['ticketsData'] as $bookedTicket) {
                $persons += $bookedTicket['persons'];
            }
        }

        $ticketsData = [];

        if (!empty($booking['ticketsData'])) {
            foreach ($booking['ticketsData'] as $ticket) {
                /** @var EventTicket $eventTicket */
                $eventTicket =
                    $event->getCustomTickets()->keyExists($ticket['eventTicketId']) ? $event->getCustomTickets()->getItem($ticket['eventTicketId']) : null;

                $ticketsData[] = [
                    'id' => $ticket['id'],
                    'eventTicketId' => $eventTicket ? $eventTicket->getId()->getValue() : null,
                    'name' => $eventTicket ? $eventTicket->getName()->getValue() : null,
                    'price' => $ticket['price'],
                    'quantity' => $ticket['persons']
                ];
            }
        }

        $bookingPaymentAmount = $reservationService->getPaymentAmount(CustomerBookingFactory::create($booking), $event, true);

        $wcTax = 0;
        $wcDiscount = 0;

        $bookingPaidPrice = 0;
        $paymentMethods   = [];
        $wcOrderUrls      = [];
        foreach ($booking['payments'] as $payment) {
            $paymentMethods[] = $payment['gateway'];

            if ($payment['status'] === 'paid' || $payment['status'] === 'partiallyPaid') {
                $bookingPaidPrice += $payment['amount'];
            }

            $paymentAS->addWcFields($payment);

            $wcTax += !empty($payment['wcItemTaxValue']) ? $payment['wcItemTaxValue'] : 0;

            $wcDiscount += !empty($payment['wcItemCouponValue']) ? $payment['wcItemCouponValue'] : 0;

            if (!empty($payment['wcOrderId'])) {
                $wcOrderUrls[$payment['wcOrderId']] = $payment['wcOrderUrl'];
            }
        }

        $total = $bookingPaymentAmount['subtotal']
            + $bookingPaymentAmount['total_tax']
            + $wcTax
            - $bookingPaymentAmount['discount']
            - $bookingPaymentAmount['deduction']
            - $wcDiscount;

        $eventBooking = [
            'id' => $booking['id'],
            'status' => $eventArray['status'] === 'canceled' || $eventArray['status'] === 'rejected' ? 'canceled' : $booking['status'],
            'persons' => $persons,
            'checked' => false,
            'tickets' => $ticketsData,
            'customer' => [
                'id' => $booking['customer']['id'],
                'firstName' => $booking['customer']['firstName'],
                'lastName' => $booking['customer']['lastName'],
                'note' => $booking['customer']['note']
            ],
            'code' => !empty($booking['token']) ? substr($booking['token'], 0, 5) : '',
            'event' => $eventArray,
            'payment' => [
                'paymentMethods' => $paymentMethods,
                'wcOrderUrls' => $wcOrderUrls,
                'status' => $paymentAS->getFullStatus($booking, BookableType::EVENT),
                'total' => $total,
                'tax' => $bookingPaymentAmount['total_tax'],
                'wcTax' => $wcTax,
                'discount' => $bookingPaymentAmount['discount'] + $bookingPaymentAmount['deduction'],
                'wcDiscount' => $wcDiscount,
                'eventPrice' => $bookingPaymentAmount['subtotal'],
                'subtotal' => $bookingPaymentAmount['subtotal'],
                'paid' => $bookingPaidPrice,
                'due' => max($total - $bookingPaidPrice, 0),
                'id' => !empty($booking['payments']) ? array_key_first($booking['payments']) : null,
            ],
            'customFields' =>
            !empty($booking['customFields']) ?
                $customFieldService->reformatCustomField(CustomerBookingFactory::create($booking), [], $customFieldsCollection) :
                null,
            'qrCodes' => !empty($booking['qrCodes']) ? $booking['qrCodes'] : null,
        ];

        if ($noShowTagEnabled && $customersNoShowCountIds) {
            /** @var CustomerBookingRepository $bookingRepository */
            $bookingRepository = $this->container->get('domain.booking.customerBooking.repository');

            $customersNoShowCount = $bookingRepository->countByNoShowStatus($customersNoShowCountIds);

            if (!empty($customersNoShowCount[$eventBooking['customer']['id']])) {
                $eventBooking['customer']['noShowCount'] = $customersNoShowCount[$eventBooking['customer']['id']]['count'];
            }
        }

        $eventBooking = apply_filters('amelia_get_event_bookings_filter', $eventBooking);

        do_action('amelia_get_event_bookings', $eventBooking);

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully retrieved event bookings');
        $result->setData(
            [
                Entities::BOOKING => $eventBooking,
            ]
        );

        return $result;
    }
}
