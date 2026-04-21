<?php

namespace AmeliaBooking\Application\Controller\Google;

use AmeliaBooking\Application\Commands\Google\VerifyRecaptchaCommand;
use AmeliaBooking\Application\Controller\Controller;
use Slim\Http\Request;

/**
 * Class VerifyRecaptchaController
 *
 * @package AmeliaBooking\Application\Controller\Google
 */
class VerifyRecaptchaController extends Controller
{
    protected $allowedFields = [
        'ameliaNonce',
        'wpAmeliaNonce',
        'secret',
        'token'
    ];

    /**
     * @param Request $request
     * @param         $args
     *
     * @return VerifyRecaptchaCommand
     */
    protected function instantiateCommand(Request $request, $args)
    {
        $command     = new VerifyRecaptchaCommand($args);
        $requestBody = $request->getParsedBody();

        if (empty($requestBody)) {
            $json = json_decode(file_get_contents('php://input'), true);
            if (is_array($json)) {
                $requestBody = $json;
            }
        }

        $this->setCommandFields($command, $requestBody);

        return $command;
    }
}
