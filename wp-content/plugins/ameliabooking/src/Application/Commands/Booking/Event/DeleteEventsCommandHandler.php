<?php

namespace AmeliaBooking\Application\Commands\Booking\Event;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Booking\EventApplicationService;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Infrastructure\Common\Exceptions\NotFoundException;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Booking\Event\EventRepository;
use AmeliaBooking\Infrastructure\WP\EventListeners\Booking\Event\EventStatusUpdatedEventHandler;
use Microsoft\Graph\Exception\GraphException;

/**
 * Class DeleteEventsCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Booking\Event
 */
class DeleteEventsCommandHandler extends CommandHandler
{
    /**
     * @param DeleteEventsCommand $command
     *
     * @return CommandResult
     * @throws AccessDeniedException
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     * @throws NotFoundException
     * @throws GraphException
     */
    public function handle(DeleteEventsCommand $command)
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

        $events = $eventApplicationService->getEventsByIds(
            $command->getField('events'),
            [
            'fetchEventsPeriods'   => true,
            'fetchEventsTickets'   => true,
            'fetchEventsTags'      => true,
            'fetchEventsProviders' => true,
            'fetchEventsImages'    => true,
            'fetchBookings'        => true,
            'fetchBookingsUsers'   => true,
            ]
        );

        do_action('amelia_before_events_deleted', $events->toArray());

        $eventRepository->beginTransaction();

        try {
            $updatedEvents = $eventApplicationService->updateStatus(
                $events,
                'rejected',
                false
            );

            $statusResult = new CommandResult();
            $statusResult->setResult(CommandResult::RESULT_SUCCESS);
            $statusResult->setData(
                [
                'status'         => 'rejected',
                Entities::EVENTS => $updatedEvents->toArray(),
                ]
            );
        } catch (QueryExecutionException $e) {
            $eventRepository->rollback();
            throw $e;
        }

        $eventRepository->commit();

        EventStatusUpdatedEventHandler::handle($statusResult, $this->container);

        $eventRepository->beginTransaction();

        try {
            $deletedEvents = [];
            foreach ($events->getItems() as $event) {
                $deletedEvents = array_merge($deletedEvents, $eventApplicationService->delete($event, $command->getField('applyGlobally'))->toArray());
            }
        } catch (QueryExecutionException $e) {
            $eventRepository->rollback();
            throw $e;
        }

        $eventRepository->commit();

        do_action('amelia_after_events_deleted', $deletedEvents);

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully deleted events');
        $result->setData(
            [
            Entities::EVENT => null,
            'deletedEvents' => $deletedEvents
            ]
        );

        return $result;
    }
}
