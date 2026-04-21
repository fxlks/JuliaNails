<?php

namespace AmeliaBooking\Application\Commands\Booking\Event;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Booking\EventApplicationService;
use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Booking\Event\Event;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Booking\Event\EventRepository;
use AmeliaBooking\Infrastructure\WP\EventListeners\Booking\Event\EventStatusUpdatedEventHandler;

/**
 * Class DeleteEventCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Booking\Event
 */
class DeleteEventCommandHandler extends CommandHandler
{
    /**
     * @param DeleteEventCommand $command
     *
     * @return CommandResult
     * @throws \Slim\Exception\ContainerValueNotFoundException
     * @throws AccessDeniedException
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     */
    public function handle(DeleteEventCommand $command)
    {
        if (!$command->getPermissionService()->currentUserCanDelete(Entities::EVENTS)) {
            throw new AccessDeniedException('You are not allowed to delete event');
        }

        $result = new CommandResult();

        $this->checkMandatoryFields($command);

        /** @var EventApplicationService $eventApplicationService */
        $eventApplicationService = $this->container->get('application.booking.event.service');

        /** @var EventRepository $eventRepository */
        $eventRepository = $this->container->get('domain.booking.event.repository');

        $event = $eventRepository->getById($command->getArg('id'));

        do_action('amelia_before_event_deleted', $event ? $event->toArray() : null);

        $eventRepository->beginTransaction();

        try {
            if ($event->getStatus()->getValue() === 'approved') {
                $events = new Collection();
                $events->addItem($event, $event->getId()->getValue());
                /** @var Collection $updatedEvents */
                $updatedEvents = $eventApplicationService->updateStatus(
                    $events,
                    'rejected',
                    $command->getField('applyGlobally')
                );

                $statusResult = new CommandResult();
                $statusResult->setResult(CommandResult::RESULT_SUCCESS);
                $statusResult->setData(
                    [
                    'status'         => 'rejected',
                    Entities::EVENTS => $updatedEvents->toArray(),
                    ]
                );

                EventStatusUpdatedEventHandler::handle($statusResult, $this->container);
            }
        } catch (QueryExecutionException $e) {
            $eventRepository->rollback();
            throw $e;
        }

        $eventRepository->commit();

        $eventRepository->beginTransaction();

        try {
            $deletedEvents = $eventApplicationService->delete($event, $command->getField('applyGlobally'));
        } catch (QueryExecutionException $e) {
            $eventRepository->rollback();
            throw $e;
        }

        $eventRepository->commit();

        do_action('amelia_after_event_deleted', $event ? $event->toArray() : null);

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully deleted event');
        $result->setData(
            [
            Entities::EVENT => $event->toArray(),
            'deletedEvents' => $deletedEvents
            ]
        );

        return $result;
    }
}
