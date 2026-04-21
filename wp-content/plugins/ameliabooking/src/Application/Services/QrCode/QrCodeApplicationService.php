<?php

namespace AmeliaBooking\Application\Services\QrCode;

use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Booking\Event\CustomerBookingEventTicket;
use AmeliaBooking\Domain\Services\DateTime\DateTimeService;
use AmeliaBooking\Infrastructure\Common\Exceptions\NotFoundException;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Booking\Event\EventTicketRepository;
use AmeliaBooking\Infrastructure\Repository\Location\LocationRepository;
use AmeliaBooking\Infrastructure\Services\QrCode\QrCodeInfrastructureService;
use Interop\Container\Exception\ContainerException;

/**
 * Class QrCodeApplicationService
 *
 * @package AmeliaBooking\Application\Services\QrCode
 */
class QrCodeApplicationService extends AbstractQrCodeApplicationService
{
    /**
     * @param array  $eventData
     * @param array  $booking
     * @param string $ticketCode
     *
     * @return array
     *
     * @throws NotFoundException
     * @throws QueryExecutionException
     * @throws ContainerException
     * @throws InvalidArgumentException
     */
    public function createQrCodeEventTickets($eventData, $booking, $ticketCode = ''): array
    {
        /** @var QrCodeInfrastructureService $qrService */
        $qrService = $this->container->get('infrastructure.qrcode.service');

        $locale = is_string($booking['info']) ? json_decode($booking['info'], true)['locale'] : '';
        $qrCodeItems = [];
        $qrJson = $booking['qrCodes'];
        $qrArr = is_string($qrJson) ? json_decode($qrJson, true) : (is_array($qrJson) ? $qrJson : []);

        if (is_array($qrArr)) {
            foreach ($qrArr as $qr) {
                if (!$ticketCode || hash_equals($qr['ticketManualCode'], $ticketCode)) {
                    $qrData = $qr;
                    if (!empty($qr['qrCodeData'])) {
                        $eventTranslations = $eventData['translations'] ? json_decode($eventData['translations'], true) : null;
                        $qrData['bookingId'] = $booking['id'];
                        $qrData['eventName'] =  !empty($eventTranslations['name'][$locale])
                            ? $eventTranslations['name'][$locale]
                            : $eventData['name'];
                        $qrData['eventStartDateTime'] = $eventData['periods'][0]['periodStart'];

                        if (
                            $qrData['type'] === 'ticket' &&
                            array_key_exists('eventTicketId', $qrData) &&
                            isset($booking['ticketsData'])
                        ) {
                            foreach ($eventData['customTickets'] as $ticket) {
                                if ($ticket['id'] === $qrData['eventTicketId']) {
                                    $ticketTranslations = $ticket['translations'] ? json_decode($ticket['translations'], true) : null;
                                    $ticketName = !empty($ticketTranslations[$locale])
                                        ? $ticketTranslations[$locale]
                                        : $ticket['name'];
                                    $qrData['eventTicketName'] = $ticketName;
                                    break;
                                }
                            }
                        }

                        if (isset($eventData['customLocation']) && $eventData['customLocation']) {
                            $qrData['eventLocation'] = $eventData['customLocation'];
                        }
                        if (isset($eventData['location'])) {
                            $qrData['eventLocation'] = $eventData['location'];
                        }
                        if (isset($eventData['locationId'])) {
                            /** @var LocationRepository $locationRepository */
                            $locationRepository = $this->container->get('domain.locations.repository');
                            $location = $locationRepository->getById($eventData['locationId']);
                            if ($location) {
                                $locationTranslations = $location->getTranslations() ? json_decode($location->getTranslations()->getValue(), true) : null;
                                $locTranslation = $locationTranslations['name'][$locale] ?? $location->getName()->getValue();
                                $qrData['eventLocation'] = $location->getAddress()->getValue() ?: $locTranslation;
                            }
                        }
                        if ($ticketCode) {
                            return $qrService->generateQrCode($qrData);
                        }
                        $qrCodeItems[] = $qrService->generateQrCode($qrData);
                    }
                }
            }
        }

        return $qrCodeItems;
    }

    /**
     * Create QR code data array for a booking and event
     *
     * @param $event
     * @param $booking
     * @return array
     * @throws InvalidArgumentException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws QueryExecutionException
     */
    public function createQrCodeEventData($event, $booking): array
    {
        $qrCodes = [];
        $qrNumberData = $this->getNumberOfQrCodes($booking, $event);

        if ($qrNumberData['number'] > 0 && $event->getId()) {
            // Common timestamp for generation moment (UTC ISO8601)
            $generatedAt = DateTimeService::getNowDateTimeObjectInUtc()->format('Y-m-d H:i:s');

            $bookingIdVal  = $booking->getId()->getValue();
            $eventIdVal    = $event->getId()->getValue();
            $customerIdVal = $booking->getCustomerId() ? $booking->getCustomerId()->getValue() : '';

            // Booking-level
            if ($qrNumberData['number'] > 1) {
                $bookingManualCode = $this->generateManualCode([
                    'bookingId' => $bookingIdVal,
                    'eventId' => $eventIdVal,
                    'customerId' => $customerIdVal,
                    'generatedAt' => $generatedAt,
                ]);

                $bookingQrData = 'type: booking | bookingId:' . $bookingIdVal . ' | ticketManualCode:' . $bookingManualCode;

                $qrCodes[] = [
                    'type' => 'booking',
                    'eventName' => $event->getName() ? $event->getName()->getValue() : '',
                    'ticketManualCode' => $bookingManualCode,
                    'qrCodeData' => $bookingQrData,
                    'generatedAt' => $generatedAt,
                    'dates' => [],
                ];
            }

            // Person / ticket level
            for ($i = 1; $i <= $qrNumberData['number']; $i++) {
                $ticketIdForPerson = $qrNumberData['ticketIds'][$i - 1] ?? null;
                $codePayload = [
                    'bookingId'   => $bookingIdVal,
                    'eventId'     => $eventIdVal,
                    'customerId'  => $customerIdVal,
                    'ticketIndex' => $i,
                    'generatedAt' => $generatedAt,
                ];

                if ($ticketIdForPerson) {
                    $codePayload['ticketId'] = $ticketIdForPerson;
                }

                $manualTicketCode = $this->generateManualCode($codePayload);

                $ticketQrData = 'type: ticket | bookingId:' . $bookingIdVal . ' ticketManualCode:' . $manualTicketCode;

                $entry = [
                    'type'             => 'ticket',
                    'eventName'        => $event->getName() ? $event->getName()->getValue() : '',
                    'ticketManualCode' => $manualTicketCode,
                    'qrCodeData'       => $ticketQrData,
                    'generatedAt'      => $generatedAt,
                    'dates'            => []
                ];

                if ($ticketIdForPerson) {
                    /** @var EventTicketRepository $eventTicketRepository */
                    $eventTicketRepository = $this->container->get('domain.booking.event.ticket.repository');
                    $ticket = $eventTicketRepository->getById($ticketIdForPerson);
                    $entry['eventTicketId'] = $ticketIdForPerson;
                    if ($ticket && $ticket->getName()) {
                        $entry['eventTicketName'] = $ticket->getName()->getValue();
                    }
                }

                $qrCodes[] = $entry;
            }
        }

        return $qrCodes;
    }

    /**
     * Determine number of QR codes to generate for a booking, and build a sequence of ticket ids if applicable
     *
     * @param $booking
     * @param $event
     * @return array ['number' => int, 'ticketIds' => array|null]
     */
    private function getNumberOfQrCodes($booking, $event): array
    {
        $personsForQr = 0;
        $ticketIdSequence = [];
        if ($booking->getTicketsBooking() && $event->getCustomPricing()->getValue()) {
            /** @var CustomerBookingEventTicket $ticketBooking */
            foreach ($booking->getTicketsBooking()->getItems() as $ticketBooking) {
                $ticketPersons = ($ticketBooking->getPersons() ? $ticketBooking->getPersons()->getValue() : 0);
                $personsForQr += $ticketPersons;
                // Build a flat sequence of ticket ids, one per person, to map each QR to a ticket
                if ($ticketPersons && $ticketBooking->getEventTicketId()) {
                    for ($ti = 0; $ti < $ticketPersons; $ti++) {
                        $ticketIdSequence[] = $ticketBooking->getEventTicketId()->getValue();
                    }
                }
            }
        } else {
            $personsForQr = $booking->getPersons() ? $booking->getPersons()->getValue() : 0;
        }

        $qrCodesNumberData['number'] = $personsForQr;
        $qrCodesNumberData['ticketIds'] = $ticketIdSequence ?: null;

        return $qrCodesNumberData;
    }

    /**
     * Generate a short manual code from data array
     *
     * @param array $data
     * @return string
     */
    private function generateManualCode($data): string
    {
        $raw = hash('sha256', json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), true);
        $b64 = rtrim(strtr(base64_encode($raw), '+/', 'AZ'), '=');

        return substr($b64, 0, 10);
    }
}
