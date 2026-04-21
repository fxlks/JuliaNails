<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Domain\Entity\Settings;

/**
 * Class PaymentPayPalSettings
 *
 * @package AmeliaBooking\Domain\Entity\Settings
 */
class PaymentPayPalSettings
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
