<?php

namespace AmeliaBooking\Application\Commands\Square;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Validation\ValidationService;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Infrastructure\Common\Exceptions\NotFoundException;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Services\Payment\SquareService;
use Slim\Exception\ContainerException;

/**
 * Class DisconnectFromSquareAccountCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Square
 */
class DisconnectFromSquareAccountCommandHandler extends CommandHandler
{
    /**
     * @param DisconnectFromSquareAccountCommand $command
     *
     * @return CommandResult
     * @throws AccessDeniedException
     * @throws NotFoundException
     * @throws QueryExecutionException
     * @throws ContainerException
     * @throws InvalidArgumentException
     * @throws \Exception
     */
    public function handle(DisconnectFromSquareAccountCommand $command)
    {
        $data = $command->getField('data');

        if (!ValidationService::verifySignature(json_encode($data), 'middleware', $command->getField('signature'))) {
            throw new AccessDeniedException('Signature mismatch.');
        }

        /** @var SquareService $squareService */
        $squareService = $this->container->get('infrastructure.payment.square.service');

        /** @var SettingsService $settingsService */
        $settingsService = $this->container->get('domain.settings.service');

        $result = new CommandResult();

        if (
            !empty($settingsService->getCategorySettings('payments')['square']['accessToken']) &&
            !$squareService->disconnectAccount(true)
        ) {
            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setMessage('Unable to disconnect from Square account.');
            $result->setData(['success' => false]);

            return $result;
        }

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully logged out of Square account');
        $result->setData(['success' => true]);

        return $result;
    }
}
