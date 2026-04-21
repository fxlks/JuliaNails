<?php

namespace AmeliaVendor\Sabre\VObject\TimezoneGuesser;

use DateTimeZone;
use AmeliaVendor\Sabre\VObject\Component\VTimeZone;

interface TimezoneGuesser
{
    public function guess(VTimeZone $vtimezone, bool $failIfUncertain = false): ?DateTimeZone;
}
