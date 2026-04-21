<?php

namespace AmeliaVendor\Stripe\Util;

class EventTypes
{
    const thinEventMapping = [
        // The beginning of the section generated from our OpenAPI spec
        \AmeliaVendor\Stripe\Events\V1BillingMeterErrorReportTriggeredEvent::LOOKUP_TYPE => \AmeliaVendor\Stripe\Events\V1BillingMeterErrorReportTriggeredEvent::class,
        \AmeliaVendor\Stripe\Events\V1BillingMeterNoMeterFoundEvent::LOOKUP_TYPE => \AmeliaVendor\Stripe\Events\V1BillingMeterNoMeterFoundEvent::class,
        \AmeliaVendor\Stripe\Events\V2CoreEventDestinationPingEvent::LOOKUP_TYPE => \AmeliaVendor\Stripe\Events\V2CoreEventDestinationPingEvent::class,
        // The end of the section generated from our OpenAPI spec
    ];
}
