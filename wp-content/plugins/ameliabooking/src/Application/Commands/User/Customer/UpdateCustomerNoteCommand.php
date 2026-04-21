<?php

namespace AmeliaBooking\Application\Commands\User\Customer;

use AmeliaBooking\Application\Commands\Command;

/**
 * Class UpdateCustomerNoteCommand
 *
 * @package AmeliaBooking\Application\Commands\User\Customer
 */
class UpdateCustomerNoteCommand extends Command
{
    /**
     * UpdateCustomerNoteCommand constructor.
     *
     * @param $args
     */
    public function __construct($args)
    {
        parent::__construct($args);
    }
}
