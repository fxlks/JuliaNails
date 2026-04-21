<?php

namespace AmeliaBooking\Infrastructure\Common;

use AmeliaBooking\Domain\Repository\User\UserRepositoryInterface;
use AmeliaBooking\Domain\Services\Logger\LoggerInterface;
use AmeliaBooking\Infrastructure\Connection;

/**
 * Class Container
 *
 * @package AmeliaBooking\Infrastructure\Common
 */
final class Container extends \Slim\Container
{
    /**
     * @return Connection
     */
    public function getDatabaseConnection()
    {
        return $this->get('app.connection');
    }

    /**
     * @return UserRepositoryInterface
     */
    public function getUserRepository()
    {
        return $this->get('domain.users.repository');
    }

    /**
     * Get the command bus
     *
     * @return mixed
     */
    public function getCommandBus()
    {
        return $this->get('command.bus');
    }

    /**
     * Get the event bus
     *
     * @return mixed
     */
    public function getEventBus()
    {
        return $this->get('domain.event.bus');
    }

    /**
     * Get the Permissions domain service
     *
     */
    public function getPermissionsService()
    {
        return $this->get('domain.permissions.service');
    }

    /**
     * Get the API Permissions domain service
     *
     */
    public function getApiPermissionsService()
    {
        return $this->get('domain.api.permissions.service');
    }

    /**
     * Get the API User application service
     *
     */
    public function getApiUserApplicationService()
    {
        return $this->get('application.api.user.service');
    }

    /**
     * Get the User application service
     *
     */
    public function getUserApplicationService()
    {
        return $this->get('application.user.service');
    }

    /**
     * Get the Logger service
     *
     * @return LoggerInterface
     */
    public function getLoggerService()
    {
        return $this->get('infrastructure.logger');
    }

    /**
     * @return mixed
     */
    public function getMailerService()
    {
        return $this->get('application.mailer');
    }

    /**
     * @return mixed
     */
    public function getSettingsService()
    {
        return $this->get('domain.settings.service');
    }
}
