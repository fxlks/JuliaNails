<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Domain\Entity\Settings;

/**
 * Class PaymentStripeSettings
 *
 * @package AmeliaBooking\Domain\Entity\Settings
 */
class PaymentStripeSettings
{
    /** @var bool */
    private $enabled;

    /**
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'enabled' => $this->enabled,
        ];
    }
}
