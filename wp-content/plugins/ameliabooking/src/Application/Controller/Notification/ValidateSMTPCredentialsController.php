<?php

namespace AmeliaBooking\Application\Controller\Notification;

use AmeliaBooking\Application\Commands\Notification\ValidateSMTPCredentialsCommand;
use AmeliaBooking\Application\Controller\Controller;
use Slim\Http\Request;

/**
 * Class ValidateSMTPCredentialsController
 *
 * @package AmeliaBooking\Application\Controller\Notification
 */
class ValidateSMTPCredentialsController extends Controller
{
    /**
     * Fields for SMTP validation that can be received from front-end
     *
     * @var array
     */
    protected $allowedFields = [
        'smtpHost',
        'smtpPort',
        'smtpSecure',
        'smtpUsername',
        'smtpPassword',
    ];

    /**
     * Instantiates the Validate SMTP Credentials command to hand it over to the Command Handler
     *
     * @param Request $request
     * @param         $args
     *
     * @return ValidateSMTPCredentialsCommand
     * @throws \RuntimeException
     */
    protected function instantiateCommand(Request $request, $args)
    {
        $command     = new ValidateSMTPCredentialsCommand($args);
        $requestBody = $request->getParsedBody();
        $this->setCommandFields($command, $requestBody);

        return $command;
    }
}
