<?php

namespace AmeliaBooking\Infrastructure\Routes\Calendar;

use AmeliaBooking\Application\Controller\Calendar\ManageCalendarBlockTimeController;
use AmeliaBooking\Application\Controller\Calendar\DeleteBlockTimeController;
use AmeliaBooking\Application\Controller\Calendar\GetBlockTimeController;
use AmeliaBooking\Application\Controller\Calendar\GetCalendarEventsController;
use AmeliaBooking\Application\Controller\Calendar\GetCalendarSlotAvailabilityController;
use AmeliaBooking\Application\Controller\Calendar\GetCalendarSlotEntitiesController;
use AmeliaBooking\Application\Controller\Calendar\GetCalendarSlotsController;
use Slim\App;

class Calendar
{
    /**
     * @param App $app
     */
    public static function routes(App $app): void
    {
        $app->get('/calendar/events', GetCalendarEventsController::class);
        $app->get('/calendar/slots', GetCalendarSlotsController::class);
        $app->get('/calendar/availability', GetCalendarSlotAvailabilityController::class);
        $app->get('/calendar/entities', GetCalendarSlotEntitiesController::class);
        $app->post('/calendar/block', ManageCalendarBlockTimeController::class);
        $app->get('/calendar/block/{id:[0-9]+}', GetBlockTimeController::class);
        $app->post('/calendar/block/delete/{id:[0-9]+}', DeleteBlockTimeController::class);
    }
}
