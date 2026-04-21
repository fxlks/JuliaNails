<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See COPYING.md for license details.
 */

namespace AmeliaBooking\Infrastructure\Routes\QrCode;

use AmeliaBooking\Application\Controller\QrCode\GetQrCodeController;
use AmeliaBooking\Application\Controller\QrCode\ScanQrCodeController;
use Slim\App;

/**
 * Class QrCode
 *
 * @package AmeliaBooking\Infrastructure\Routes\QrCode
 */
class QrCode
{
    /**
     * @param App $app
     */
    public static function routes(App $app)
    {
        $app->post('/scan-eticket', ScanQrCodeController::class);

        $app->get('/etickets', GetQrCodeController::class);
    }
}
