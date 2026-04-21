<?php

namespace AmeliaBooking\Domain\Factory\Location;

use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Location\Location;
use AmeliaBooking\Domain\Factory\Bookable\Service\ServiceFactory;
use AmeliaBooking\Domain\Factory\Booking\Event\EventFactory;
use AmeliaBooking\Domain\Factory\User\UserFactory;
use AmeliaBooking\Domain\Services\DateTime\DateTimeService;
use AmeliaBooking\Domain\ValueObjects\Json;
use AmeliaBooking\Domain\ValueObjects\String\Status;
use AmeliaBooking\Domain\ValueObjects\Picture;
use AmeliaBooking\Domain\ValueObjects\String\Address;
use AmeliaBooking\Domain\ValueObjects\String\Description;
use AmeliaBooking\Domain\ValueObjects\GeoTag;
use AmeliaBooking\Domain\ValueObjects\Number\Integer\Id;
use AmeliaBooking\Domain\ValueObjects\String\Name;
use AmeliaBooking\Domain\ValueObjects\String\Phone;
use AmeliaBooking\Domain\ValueObjects\String\Url;

/**
 * Class LocationFactory
 *
 * @package AmeliaBooking\Domain\Factory\Location
 */
class LocationFactory
{
    /**
     * @param $data
     *
     * @return Location
     * @throws InvalidArgumentException
     */
    public static function create($data)
    {
        $location = new Location();

        if (isset($data['id'])) {
            $location->setId(new Id($data['id']));
        }

        if (isset($data['name'])) {
            $location->setName(new Name($data['name']));
        }

        if (isset($data['countryPhoneIso'])) {
            $location->setCountryPhoneIso(new Name($data['countryPhoneIso']));
        }

        if (isset($data['address'])) {
            $location->setAddress(new Address($data['address']));
        }

        $location->setPhone(new Phone($data['phone'] ?? ''));

        if (isset($data['latitude'], $data['longitude'])) {
            $location->setCoordinates(new GeoTag($data['latitude'], $data['longitude']));
        }

        if (isset($data['description'])) {
            $location->setDescription(new Description($data['description']));
        }

        if (isset($data['status'])) {
            $location->setStatus(new Status($data['status']));
        }

        if (!empty($data['pictureFullPath']) && !empty($data['pictureThumbPath'])) {
            $location->setPicture(new Picture($data['pictureFullPath'], $data['pictureThumbPath']));
        }

        if (isset($data['pin'])) {
            $location->setPin(new Url($data['pin']));
        }

        if (!empty($data['translations'])) {
            $location->setTranslations(new Json($data['translations']));
        }

        if (isset($data['serviceList'])) {
            $serviceList = [];
            foreach ((array)$data['serviceList'] as $service) {
                $serviceList[$service['id']] = ServiceFactory::create($service);
            }

            $location->setServiceList(new Collection($serviceList));
        }

        if (isset($data['eventList'])) {
            $eventsList = [];
            foreach ((array)$data['eventList'] as $event) {
                $eventsList[$event['id']] = EventFactory::create($event);
            }

            $location->setEventList(new Collection($eventsList));
        }

        if (isset($data['providerList'])) {
            $providerList = [];
            foreach ((array)$data['providerList'] as $provider) {
                $providerList[$provider['id']] = UserFactory::create($provider);
            }

            $location->setProviderList(new Collection($providerList));
        }

        return $location;
    }


    /**
     * @param array $rows
     *
     * @return Collection
     * @throws InvalidArgumentException
     */
    public static function createCollection($rows)
    {
        $locations = [];

        foreach ($rows as $row) {
            $locationId = $row['location_id'];

            $providerId = $row['provider_id'];

            $serviceId = $row['service_id'];

            $eventId = $row['event_id'];

            $eventPeriodId = $row['event_periodId'];

            if (!array_key_exists($locationId, $locations)) {
                $locations[$locationId] = [
                    'id' => $row['location_id'],
                    'name' => $row['location_name'],
                    'address' => $row['location_address'],
                    'phone' => $row['location_phone'],
                    'latitude' => $row['location_latitude'],
                    'longitude' => $row['location_longitude'],
                    'description' => $row['location_description'],
                    'status' => $row['location_status'],
                    'pictureFullPath' => $row['location_pictureFullPath'],
                    'pictureThumbPath' => $row['location_pictureThumbPath'],
                    'pin' => $row['location_pin'],
                    'translations' => $row['location_translations']
                ];
            }

            if ($serviceId && empty($locations[$locationId]['serviceList'][$serviceId])) {
                $serviceId = $row['service_id'];

                $locations[$locationId]['serviceList'][$serviceId] = [
                    'id' => $serviceId,
                    'name' => $row['service_name'],
                    'category' => [
                        'name' => $row['category_name']
                    ],
                    'color' => $row['service_color']
                ];
            }

            if (!empty($row['provider_id']) && empty($locations[$locationId]['providerList'][$providerId])) {
                $locations[$locationId]['providerList'][$providerId] = [
                    'id' => $providerId,
                    'firstName' => $row['provider_firstName'],
                    'lastName' => $row['provider_lastName'],
                    'phone' => $row['provider_phone'],
                    'email' => $row['provider_email'],
                    'type' => 'provider',
                    'pictureThumbPath' => $row['provider_pictureThumbPath'],
                    'pictureFullPath' => $row['provider_pictureFullPath']
                ];
            }

            if ($eventId && empty($locations[$locationId]['eventList'][$eventId])) {
                $locations[$locationId]['eventList'][$eventId] = [
                    'id' => $eventId,
                    'name' => $row['event_name'],
                    'color' => $row['event_color'],
                ];
            }

            if ($eventPeriodId && !isset($locations[$locationId]['eventList'][$eventId]['periods'][$eventPeriodId])) {
                $zoomMeetingJson = !empty($row['event_periodZoomMeeting']) ?
                    json_decode($row['event_periodZoomMeeting'], true) : null;

                $locations[$locationId]['eventList'][$eventId]['periods'][$eventPeriodId] = [
                    'id'             => $eventPeriodId,
                    'periodStart'    => DateTimeService::getCustomDateTimeFromUtc($row['event_periodStart']),
                    'periodEnd'      => DateTimeService::getCustomDateTimeFromUtc($row['event_periodEnd']),
                    'zoomMeeting'    => [
                        'id'       => $zoomMeetingJson ? $zoomMeetingJson['id'] : null,
                        'startUrl' => $zoomMeetingJson ? $zoomMeetingJson['startUrl'] : null,
                        'joinUrl'  => $zoomMeetingJson ? $zoomMeetingJson['joinUrl'] : null,
                    ],
                    'lessonSpace'    => !empty($row['event_periodLessonSpace']) ?
                        $row['event_periodLessonSpace'] : null,
                    'googleMeetUrl'     => !empty($row['event_googleMeetUrl']) ?
                        $row['event_googleMeetUrl'] : null
                ];
            }
        }

        /** @var Collection $collection */
        $collection = new Collection();

        foreach ($locations as $key => $value) {
            $collection->addItem(
                self::create($value),
                $key
            );
        }

        return $collection;
    }
}
