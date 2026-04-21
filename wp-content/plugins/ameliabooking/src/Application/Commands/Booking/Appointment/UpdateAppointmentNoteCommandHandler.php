<?php

namespace AmeliaBooking\Application\Commands\Booking\Appointment;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\User\UserApplicationService;
use AmeliaBooking\Domain\Common\Exceptions\AuthorizationException;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Entity\User\AbstractUser;
use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Booking\Appointment\AppointmentRepository;

/**
 * Class UpdateAppointmentNoteCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Booking\Appointment
 */
class UpdateAppointmentNoteCommandHandler extends CommandHandler
{
    /**
     * @param UpdateAppointmentNoteCommand $command
     *
     * @return CommandResult
     * @throws AccessDeniedException
     * @throws AuthorizationException
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     */
    public function handle(UpdateAppointmentNoteCommand $command)
    {
        $result = new CommandResult();

        /** @var UserApplicationService $userAS */
        $userAS = $this->getContainer()->get('application.user.service');
        /** @var SettingsService $settingsDS */
        $settingsDS = $this->container->get('domain.settings.service');
        /** @var AppointmentRepository $appointmentRepo */
        $appointmentRepo = $this->container->get('domain.booking.appointment.repository');

        try {
            /** @var AbstractUser $user */
            $user = $command->getUserApplicationService()->authorization(
                $command->getPage() === 'cabinet' ? $command->getToken() : null,
                $command->getCabinetType()
            );
        } catch (AuthorizationException $e) {
            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setData(['reauthorize' => true]);

            return $result;
        }

        if ($userAS->isCustomer($user)) {
            throw new AccessDeniedException('You are not allowed to update appointment');
        }

        if ($userAS->isProvider($user) && !$settingsDS->getSetting('roles', 'allowWriteAppointments')) {
            throw new AccessDeniedException('You are not allowed to update appointment');
        }

        $appointmentId  = (int)$command->getArg('id');
        $note           = $command->getField('internalNotes');
        $oldAppointment = $appointmentRepo->getById($appointmentId);

        if ($oldAppointment === null) {
            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setMessage('Appointment not found');
            return $result;
        }

        $appointmentRepo->updateFieldById($appointmentId, $note, 'internalNotes');

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully updated appointment note');
        $result->setData([
            Entities::APPOINTMENT => array_merge(
                $oldAppointment->toArray(),
                ['internalNotes' => $note]
            ),
        ]);

        return $result;
    }
}
