<?php

namespace AmeliaBooking\Application\Commands\Notification;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaVendor\PHPMailer\PHPMailer\PHPMailer;
use AmeliaVendor\PHPMailer\PHPMailer\Exception as PHPMailerException;
use Exception;

/**
 * Class ValidateSMTPCredentialsCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Notification
 */
class ValidateSMTPCredentialsCommandHandler extends CommandHandler
{
    /** @var array */
    public $mandatoryFields = [
        'smtpHost',
        'smtpPort',
        'smtpUsername',
        'smtpPassword',
    ];

    /**
     * @param ValidateSMTPCredentialsCommand $command
     *
     * @return CommandResult
     * @throws AccessDeniedException|InvalidArgumentException
     */
    public function handle(ValidateSMTPCredentialsCommand $command): CommandResult
    {
        if (!$this->getContainer()->getPermissionsService()->currentUserCanWrite(Entities::SETTINGS)) {
            throw new AccessDeniedException('You are not allowed to validate SMTP credentials.');
        }

        $result = new CommandResult();

        $this->checkMandatoryFields($command);

        $smtpHost     = $command->getField('smtpHost');
        $smtpPort     = $command->getField('smtpPort');
        $smtpSecure   = $command->getField('smtpSecure');
        $smtpUsername = $command->getField('smtpUsername');
        $smtpPassword = $command->getField('smtpPassword');

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->SMTPAuth   = true;
            $mail->SMTPSecure = $smtpSecure ?: '';
            $mail->Host       = $smtpHost;
            $mail->Port       = $smtpPort;
            $mail->Username   = $smtpUsername;
            $mail->Password   = $smtpPassword;
            $mail->Timeout    = 10;

            if (!$mail->smtpConnect()) {
                $result->setResult(CommandResult::RESULT_ERROR);
                $result->setMessage('Failed to connect to SMTP server.');

                return $result;
            }

            $mail->smtpClose();

            $result->setResult(CommandResult::RESULT_SUCCESS);
            $result->setMessage('SMTP credentials are valid.');
        } catch (PHPMailerException | Exception $e) {
            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setMessage('SMTP validation failed: ' . $e->getMessage());
        }

        return $result;
    }
}
