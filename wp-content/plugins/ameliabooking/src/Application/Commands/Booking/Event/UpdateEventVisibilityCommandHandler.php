<?php

namespace AmeliaBooking\Application\Commands\Booking\Event;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Booking\Event\Event;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Booking\Event\EventRepository;

/**
 * Class UpdateEventVisibilityCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Booking\Event
 */
class UpdateEventVisibilityCommandHandler extends CommandHandler
{
    /**
     * @var array
     */
    public $mandatoryFields = [
        'status'
    ];

    /**
     * @param UpdateEventVisibilityCommand $command
     *
     * @return CommandResult
     * @throws \Slim\Exception\ContainerException
     * @throws \InvalidArgumentException
     * @throws \Slim\Exception\ContainerValueNotFoundException
     * @throws QueryExecutionException
     * @throws InvalidArgumentException
     * @throws AccessDeniedException
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function handle(UpdateEventVisibilityCommand $command)
    {
        if (!$command->getPermissionService()->currentUserCanWriteStatus(Entities::EVENTS)) {
            throw new AccessDeniedException('You are not allowed to update event status');
        }

        $result = new CommandResult();

        $this->checkMandatoryFields($command);

        /** @var EventRepository $eventRepository */
        $eventRepository = $this->container->get('domain.booking.event.repository');

        $requestedStatus = $command->getField('status');
        $applyGlobally   = $command->getField('applyGlobally');

        $eventId = (int)$command->getArg('id');

        /** @var Event $event */
        $event = $eventRepository->getById($eventId);

        $eventRepository->beginTransaction();

        do_action('amelia_before_event_status_updated', $event ? $event->toArray() : null, $requestedStatus, $command->getField('applyGlobally'));

        try {
            $eventRepository->toggleEventVisibility($event, $requestedStatus === 'hidden' ? 0 : 1, $applyGlobally);
        } catch (QueryExecutionException $e) {
            $eventRepository->rollback();
            throw $e;
        }

        $eventRepository->commit();

        do_action('amelia_after_event_status_updated', $event ? $event->toArray() : null, $requestedStatus, $command->getField('applyGlobally'));

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully updated event visibility');
        $result->setData(true);

        return $result;
    }
}
