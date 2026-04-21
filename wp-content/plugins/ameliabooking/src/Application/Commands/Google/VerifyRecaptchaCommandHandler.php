<?php

namespace AmeliaBooking\Application\Commands\Google;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Domain\Entity\Entities;

/**
 * Class VerifyRecaptchaCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Google
 */
class VerifyRecaptchaCommandHandler extends CommandHandler
{
    /**
     * @param VerifyRecaptchaCommand $command
     *
     * @return CommandResult
     * @throws AccessDeniedException
     */
    public function handle(VerifyRecaptchaCommand $command)
    {
        $result = new CommandResult();

        if (!$this->getContainer()->getPermissionsService()->currentUserCanWrite(Entities::SETTINGS)) {
            throw new AccessDeniedException('You are not allowed to read settings.');
        }

        $fields = $command->getFields();
        $secret = isset($fields['secret']) ? $fields['secret'] : '';
        $token  = isset($fields['token']) ? $fields['token'] : null;

        /** @var \AmeliaBooking\Infrastructure\Services\Recaptcha\AbstractRecaptchaService $recaptchaService */
        $recaptchaService = $this->getContainer()->get('infrastructure.recaptcha.service');

        $verification = $recaptchaService->verifyWithSecret($secret, $token);

        if ($verification['success']) {
            $result->setResult(CommandResult::RESULT_SUCCESS);
            $result->setMessage($verification['message']);
        } else {
            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setMessage($verification['message']);
            if (isset($verification['error_codes'])) {
                $result->setData(['error_codes' => $verification['error_codes']]);
            }
        }

        return $result;
    }
}
