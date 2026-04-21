<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Domain\Entity\Settings;

/**
 * Class PaymentMollieSettings
 *
 * @package AmeliaBooking\Domain\Entity\Settings
 */
class PaymentMollieSettings
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
