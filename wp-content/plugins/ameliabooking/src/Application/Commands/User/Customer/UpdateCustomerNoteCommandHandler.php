<?php

namespace AmeliaBooking\Application\Commands\User\Customer;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\User\UserApplicationService;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Infrastructure\Common\Exceptions\NotFoundException;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\User\UserRepository;

/**
 * Class UpdateCustomerNoteCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\User\Customer
 */
class UpdateCustomerNoteCommandHandler extends CommandHandler
{
    /**
     * @param UpdateCustomerNoteCommand $command
     *
     * @return CommandResult
     *
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws QueryExecutionException
     * @throws AccessDeniedException
     */
    public function handle(UpdateCustomerNoteCommand $command)
    {
        $result = new CommandResult();

        /** @var UserApplicationService $userAS */
        $userAS = $this->getContainer()->get('application.user.service');

        /** @var UserRepository $userRepository */
        $userRepository = $this->getContainer()->get('domain.users.repository');

        if (!$command->getPermissionService()->currentUserCanWrite(Entities::CUSTOMERS)) {
            if ($command->getToken()) {
                $provider = $command->getCabinetType() === 'provider'
                    ? $userAS->getAuthenticatedUser($command->getToken(), false, 'providerCabinet')
                    : null;

                $oldUser = $provider === null
                    ? $userAS->getAuthenticatedUser($command->getToken(), false, 'customerCabinet')
                    : $userRepository->getById($command->getArg('id'));

                if (
                    $provider === null &&
                    ($oldUser === null || $oldUser->getId()->getValue() !== intval($command->getArg('id')))
                ) {
                    $result->setResult(CommandResult::RESULT_ERROR);
                    $result->setMessage('Could not retrieve user');
                    $result->setData(['reauthorize' => true]);

                    return $result;
                }

                if ($provider !== null && $oldUser === null) {
                    $result->setResult(CommandResult::RESULT_ERROR);
                    $result->setMessage('Could not retrieve user');

                    return $result;
                }
            } else {
                throw new AccessDeniedException('You are not allowed to perform this action!');
            }
        } else {
            $oldUser = $userRepository->getById($command->getArg('id'));
            if ($oldUser === null) {
                $result->setResult(CommandResult::RESULT_ERROR);
                $result->setMessage('Could not retrieve user');
                return $result;
            }
        }

        $customerId = (int)$command->getArg('id');
        $note       = $command->getField('note');

        $userRepository->beginTransaction();

        try {
            $userRepository->updateFieldById($customerId, $note, 'note');
        } catch (QueryExecutionException $e) {
            $userRepository->rollback();
            throw $e;
        }

        $userRepository->commit();

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully updated customer note');
        $result->setData([]);

        return $result;
    }
}
