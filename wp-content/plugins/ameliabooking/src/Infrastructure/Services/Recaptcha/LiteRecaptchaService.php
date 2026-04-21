<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Infrastructure\Services\Recaptcha;

/**
 * Class RecaptchaService
 */
class LiteRecaptchaService extends AbstractRecaptchaService
{
    /**
     * @param string $value
     *
     * @return boolean
     */
    public function verify($value)
    {
        return true;
    }

    /**
     * Verify recaptcha with provided secret and token
     *
     * @param string $secret
     * @param string $token
     *
     * @return array Array with 'success' (bool) and 'message' (string)
     */
    public function verifyWithSecret($secret, $token)
    {
        return [
            'success' => true,
            'message' => 'Validation successful (Lite version)'
        ];
    }

    /**
     * @param string $value
     * @param string $cabinetType
     *
     * @return boolean
     */
    public function process($value, $cabinetType)
    {
        return true;
    }
}
