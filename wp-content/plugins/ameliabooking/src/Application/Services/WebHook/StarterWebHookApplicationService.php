<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Application\Services\WebHook;

/**
 * Class StarterWebHookApplicationService
 *
 * @package AmeliaBooking\Application\Services\WebHook
 */
class StarterWebHookApplicationService extends AbstractWebHookApplicationService
{
    /**
     * @param string   $action
     * @param array    $reservation
     * @param array    $bookings
     *
     * @return void
     */
    public function process($action, $reservation, $bookings)
    {
    }
}
