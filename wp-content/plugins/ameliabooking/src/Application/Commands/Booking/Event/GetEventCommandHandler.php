<?php

namespace AmeliaBooking\Application\Commands\Booking\Event;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Booking\EventApplicationService;
use AmeliaBooking\Application\Services\CustomField\AbstractCustomFieldApplicationService;
use AmeliaBooking\Application\Services\Payment\PaymentApplicationService;
use AmeliaBooking\Application\Services\Reservation\EventReservationService;
use AmeliaBooking\Application\Services\User\CustomerApplicationService;
use AmeliaBooking\Application\Services\User\ProviderApplicationService;
use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Common\Exceptions\AuthorizationException;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Booking\Appointment\CustomerBooking;
use AmeliaBooking\Domain\Entity\Booking\Event\CustomerBookingEventTicket;
use AmeliaBooking\Domain\Entity\Booking\Event\Event;
use AmeliaBooking\Domain\Entity\Booking\Event\EventPeriod;
use AmeliaBooking\Domain\Entity\Booking\Event\EventTicket;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Entity\User\AbstractUser;
use AmeliaBooking\Domain\ValueObjects\Number\Integer\IntegerValue;
use AmeliaBooking\Domain\ValueObjects\String\BookingStatus;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Booking\Appointment\CustomerBookingRepository;
use AmeliaBooking\Infrastructure\Repository\Booking\Event\EventRepository;
use AmeliaBooking\Infrastructure\Repository\CustomField\CustomFieldRepository;
use Exception;
use Slim\Exception\ContainerValueNotFoundException;

/**
 * Class GetEventCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Booking\Event
 */
class GetEventCommandHandler extends CommandHandler
{
    /**
     * @param GetEventCommand $command
     *
     * @return CommandResult
     * @throws ContainerValueNotFoundException
     * @throws AccessDeniedException
     * @throws QueryExecutionException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function handle(GetEventCommand $command)
    {
        $result = new CommandResult();

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

        if ($user === null) {
            throw new AccessDeniedException('You are not allowed to read events');
        }

        /** @var EventApplicationService $eventApplicationService */
        $eventApplicationService = $this->container->get('application.booking.event.service');
        /** @var EventRepository $eventRepository */
        $eventRepository = $this->container->get('domain.booking.event.repository');
        /** @var PaymentApplicationService $paymentAS */
        $paymentAS = $this->container->get('application.payment.service');
        /** @var ProviderApplicationService $providerAS */
        $providerAS = $this->container->get('application.user.provider.service');
        /** @var AbstractCustomFieldApplicationService $customFieldService */
        $customFieldService = $this->container->get('application.customField.service');
        /** @var EventReservationService $reservationService */
        $reservationService = $this->container->get('application.reservation.service')->get(Entities::EVENT);
        /** @var CustomerBookingRepository $bookingRepository */
        $bookingRepository = $this->container->get('domain.booking.customerBooking.repository');
        /** @var CustomFieldRepository $customFieldRepository */
        $customFieldRepository = $this->container->get('domain.customField.repository');

        $fetchBookings =
            empty($command->getFields()['params']['bookings']) ||
            filter_var($command->getFields()['params']['bookings'], FILTER_VALIDATE_BOOLEAN);

        /** @var Event $event */
        $event = $eventApplicationService->getEventById(
            (int)$command->getField('id'),
            [
                'fetchEventsPeriods'    => true,
                'fetchEventsTickets'    => true,
                'fetchEventsTags'       => empty($command->getFields()['params']['drawer']),
                'fetchEventsProviders'  => empty($command->getFields()['params']['drawer']),
                'fetchEventsImages'     => true,
                'fetchBookings'         => $fetchBookings,
                'fetchBookingsTickets'  => $fetchBookings,
                'fetchBookingsUsers'    => $fetchBookings,
                'fetchBookingsPayments' => $fetchBookings,
                'fetchBookingsCoupons'  => $fetchBookings,
                'fetchEventsOrganizer'  => true,
                'fetchEventsLocation'   => true,
                'fetchOccupancy'        => !$fetchBookings,
            ]
        );

        if (!$event) {
            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setMessage('Could not retrieve event');
            $result->setData(
                [
                    Entities::EVENT => []
                ]
            );

            return $result;
        }

        /** @var Collection $customFieldsCollection */
        $customFieldsCollection = $customFieldRepository->getAll([], false);

        // set tickets price by dateRange
        if ($event->getCustomTickets()->getItems()) {
            $event->setCustomTickets($eventApplicationService->getTicketsPriceByDateRange($event->getCustomTickets()));
        }

        $timeZone = !empty($command->getField('params')['timeZone'])
            ? $command->getField('params')['timeZone']
            : ($user->getType() === Entities::PROVIDER ? $providerAS->getTimeZone($user) : null);

        if ($timeZone) {
            /** @var EventPeriod $period */
            foreach ($event->getPeriods()->getItems() as $period) {
                $period->getPeriodStart()->getValue()->setTimezone(
                    new \DateTimeZone($timeZone)
                );

                $period->getPeriodEnd()->getValue()->setTimezone(
                    new \DateTimeZone($timeZone)
                );
            }
        }

        /** @var CustomerApplicationService $customerAS */
        $customerAS = $this->container->get('application.user.customer.service');

        $customerAS->removeBookingsForOtherCustomers($user, new Collection([$event]));

        $eventInfo = $eventApplicationService->getEventInfo($event);

        $eventStartDateTime = array_values($event->getPeriods()->toArray())[0]['periodStart'];

        $eventEndDateTime = array_values($event->getPeriods()->toArray())[$event->getPeriods()->length() - 1]['periodEnd'];

        $recurringEvents = [];
        if ($event->getRecurring()) {
            $recurringEvents = $eventRepository->getFilteredIds(
                [
                    'parentId' => $event->getParentId() ? $event->getParentId()->getValue() : $event->getId()->getValue(),
                    'dates' => [$eventStartDateTime]
                ],
                null
            );
        }

        $eventBookings = [];
        $bookingsPrice = 0;
        $paidPrice     = 0;

        $customersIds = [];

        /** @var CustomerBooking $booking */
        foreach ($event->getBookings()->getItems() as $booking) {
            $customersIds[] = $booking->getCustomerId()->getValue();
        }

        $customersNoShowCount = $customersIds ? $bookingRepository->countByNoShowStatus($customersIds) : [];

        /** @var CustomerBooking $booking */
        foreach ($event->getBookings()->getItems() as $booking) {
            $customFields   = [];
            $bookingPrice   = $paymentAS->calculateAppointmentPrice($booking->toArray(), 'event');
            $bookingsPrice += $bookingPrice;
            $ticketsData    = [];

            $persons = $booking->getPersons()->getValue();

            /** @var CustomerBookingEventTicket $ticket */
            foreach ($booking->getTicketsBooking()->getItems() as $ticket) {
                $persons += $ticket->getPersons()->getValue();

                /** @var EventTicket $eventTicket */
                $eventTicket = $event->getCustomTickets()->keyExists($ticket->getEventTicketId()->getValue()) ?
                    $event->getCustomTickets()->getItem($ticket->getEventTicketId()->getValue()) :
                    null;

                $ticketsData[] = [
                    'id' => $ticket->getId()->getValue(),
                    'eventTicketId' => $eventTicket ? $eventTicket->getId()->getValue() : null,
                    'name' => $eventTicket ? $eventTicket->getName()->getValue() : null,
                    'price' => $ticket->getPrice()->getValue(),
                    'quantity' => $ticket->getPersons()->getValue()
                ];
            }
            $booking->setPersons(new IntegerValue($persons));

            $bookingPaymentAmount = $reservationService->getPaymentAmount($booking, $event);

            $bookingPaidPrice = 0;
            foreach ($booking->getPayments()->toArray() as $payment) {
                if ($payment['status'] === 'paid' || $payment['status'] === 'partiallyPaid') {
                    $bookingPaidPrice += $payment['amount'];
                }
            }
            $paidPrice += $bookingPaidPrice;

            $customFields = $customFieldService->reformatCustomField($booking, $customFields, $customFieldsCollection);

            $eventBookings[] = [
                'persons' => $persons,
                'customer' => [
                    'id' => $booking->getCustomerId()->getValue(),
                    'firstName' => $booking->getCustomer()->getFirstName()->getValue(),
                    'lastName' => $booking->getCustomer()->getLastName() ? $booking->getCustomer()->getLastName()->getValue() : null,
                    'email' => $booking->getCustomer()->getEmail() ? $booking->getCustomer()->getEmail()->getValue() : null,
                    'noShowCount' => !empty($customersNoShowCount[$booking->getCustomerId()->getValue()])
                        ? $customersNoShowCount[$booking->getCustomerId()->getValue()]['count']
                        : [],
                    'note' => $booking->getCustomer()->getNote() ? $booking->getCustomer()->getNote()->getValue() : null,
                ],
                'tickets' => $ticketsData,
                'status' => in_array($event->getStatus()->getValue(), [BookingStatus::CANCELED, BookingStatus::REJECTED]) ?
                    'canceled' :
                    $booking->getStatus()->getValue(),
                'id' => $booking->getId()->getValue(),
                'customFields' => $customFields,
                'payment' => [
                'paymentMethods' => array_map(
                    function ($payment) {
                        return $payment['gateway'];
                    },
                    $booking->getPayments()->toArray()
                ),
                'status' => $paymentAS->getFullStatus($booking->toArray(), 'appointment'),
                'total' => $bookingPrice,
                'discount' => $bookingPaymentAmount['full_discount'],
                'eventPrice' => $bookingPaymentAmount['price'],
                'subtotal' => $bookingPaymentAmount['price'],
                'paid' => $bookingPaidPrice,
                'due' => max($bookingPrice - $bookingPaidPrice, 0),
                ],
                'qrCodes' => $booking->getQrCodes() ? $booking->getQrCodes()->getValue() : null,
            ];
        }

        $allEventFields = $event->toArray();

        usort(
            $allEventFields['gallery'],
            function ($picture1, $picture2) {
                return $picture1['position'] <=> $picture2['position'];
            }
        );

        $firstGalleryImage = !empty($allEventFields['gallery']) ? $allEventFields['gallery'][0]['pictureThumbPath'] : null;

        $eventArray = array_merge(
            [
                'id' => $event->getId()->getValue(),
                'name' => $event->getName()->getValue(),
                'bookings' => $eventBookings,
                'periods' => $event->getPeriods()->toArray(),
                'color' => $event->getColor() ? $event->getColor()->getValue() : null,
                'customTickets' => $event->getCustomPricing() && $event->getCustomPricing()->getValue() ? $event->getCustomTickets()->toArray() : null,
                'organizer' => $event->getOrganizerId() && $event->getOrganizer() ? $event->getOrganizer()->toArray() : null,
                'price' => $event->getPrice() ? $event->getPrice()->getValue() : null,
                'show' => $event->getShow() ? $event->getShow()->getValue() : true,
                'pictureThumbPath' => $event->getPicture() ? $event->getPicture()->getThumbPath() : $firstGalleryImage,
                'maxCapacity' => $event->getMaxCapacity() ? $event->getMaxCapacity()->getValue() : null,
                'recurringCount' => sizeof($recurringEvents) > 0 ? (sizeof($recurringEvents) - 1) : 0,
                'totalPrice' => $bookingsPrice,
                'paidPrice' => $paidPrice,
                'startDate' => explode(' ', $eventStartDateTime)[0],
                'startTime' => explode(' ', $eventStartDateTime)[1],
                'endDate' => explode(' ', $eventEndDateTime)[0],
                'location' => $event->getCustomLocation() ?
                    ['name' => $event->getCustomLocation()->getValue()] :
                    ($event->getLocationId() ? $event->getLocation()->toArray() : null),
                'payment' => $eventRepository->getEventsPaymentsSummary((int)$command->getField('id')),
            ],
            $eventInfo
        );

        $eventArray['staff'] = array_map(
            function ($provider) {
                return [
                    'id' => $provider['id'],
                    'firstName' => $provider['firstName'],
                    'lastName' => $provider['lastName'],
                    'picture' => $provider['pictureThumbPath']
                ];
            },
            $event->getProviders()->toArray()
        );

        $eventArray['organizer'] = $event->getOrganizerId() && $event->getOrganizer() ? [
            'id' => $eventArray['organizer']['id'],
            'firstName' => $eventArray['organizer']['firstName'],
            'lastName' => $eventArray['organizer']['lastName'],
            'picture' => $eventArray['organizer']['pictureThumbPath']
        ] : null;


        $eventArray = apply_filters('amelia_get_event_filter', $eventArray);

        do_action('amelia_get_event', $eventArray);



        // TODO: Redesign - remove 'drawer' condition, used for old design compatibility
        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully retrieved event');
        $result->setData(
            [
                Entities::EVENT => !empty($command->getFields()['params']['drawer'])
                    ? $eventArray
                    : $allEventFields,
            ]
        );

        return $result;
    }
}
