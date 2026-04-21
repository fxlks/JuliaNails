<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Application\Commands\Calendar;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Services\Bookable\BookableApplicationService;
use AmeliaBooking\Application\Services\Booking\AppointmentApplicationService;
use AmeliaBooking\Application\Services\User\UserApplicationService;
use AmeliaBooking\Domain\Common\Exceptions\AuthorizationException;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Bookable\Service\Service;
use AmeliaBooking\Domain\Entity\Booking\Appointment\Appointment;
use AmeliaBooking\Domain\Entity\User\AbstractUser;
use AmeliaBooking\Domain\Services\DateTime\DateTimeService;
use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Domain\ValueObjects\DateTime\DateTimeValue;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Booking\Appointment\AppointmentRepository;
use AmeliaBooking\Infrastructure\WP\Translations\FrontendStrings;
use Interop\Container\Exception\ContainerException;
use Psr\Container\ContainerExceptionInterface;

class GetCalendarSlotAvailabilityHandler extends CommandHandler
{
    /**
     * @var array
     */
    public $mandatoryFields = [
        'bookingStart',
        'appointmentId'
    ];

    /**
     * @throws ContainerException
     * @throws InvalidArgumentException
     * @throws ContainerExceptionInterface
     * @throws QueryExecutionException
     */
    public function handle(GetCalendarSlotAvailabilityCommand $command): CommandResult
    {
        $this->checkMandatoryFields($command);

        $result = new CommandResult();

        /** @var UserApplicationService $userAS */
        $userAS = $this->container->get('application.user.service');
        /** @var SettingsService $settingsDS */
        $settingsDS = $this->container->get('domain.settings.service');
        /** @var AppointmentRepository $appointmentRepo */
        $appointmentRepo = $this->container->get('domain.booking.appointment.repository');
        /** @var AppointmentApplicationService $appointmentAS */
        $appointmentAS = $this->container->get('application.booking.appointment.service');
        /** @var BookableApplicationService $bookableAS */
        $bookableAS = $this->container->get('application.bookable.service');

        try {
            /** @var AbstractUser $user */
            $user = $command->getUserApplicationService()->authorization(
                $command->getPage() === 'cabinet' ? $command->getToken() : null,
                $command->getCabinetType()
            );
        } catch (AuthorizationException $e) {
            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setData(
                [
                    'reauthorize' => true
                ]
            );

            return $result;
        }

        /** @var Appointment $appointment */
        $appointment = $appointmentRepo->getById($command->getField('appointmentId'));

        /** @var Service $service */
        $service = $bookableAS->getAppointmentService(
            $appointment->getServiceId()->getValue(),
            $appointment->getProviderId()->getValue()
        );

        $bookingStart = DateTimeService::getDateTimeObjectInTimeZone(
            $command->getField('bookingStart'),
            $command->getField('timeZone') ?: DateTimeService::getTimeZone()->getName()
        );

        $bookingEnd = (clone $bookingStart)->modify('+' . $appointmentAS->getAppointmentLengthTime($appointment, $service) . ' second');

        $bookingStart->setTimezone(DateTimeService::getTimeZone());
        $bookingEnd->setTimezone(DateTimeService::getTimeZone());

        $appointment->setBookingStart(new DateTimeValue($bookingStart));

        $appointment->setBookingEnd(new DateTimeValue($bookingEnd));

        if (!$appointmentAS->canBeBooked($appointment, $userAS->isCustomer($user), null, null)) {
            $result->setResult(CommandResult::RESULT_ERROR);
            $result->setMessage(FrontendStrings::getCommonStrings()['time_slot_unavailable']);
            $result->setData(
                [
                    'timeSlotUnavailable' => true
                ]
            );

            return $result;
        }

        return $result;
    }
}
