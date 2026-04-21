<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Domain\Entity\Schedule;

use AmeliaBooking\Domain\Entity\User\Provider;
use AmeliaBooking\Domain\ValueObjects\Number\Integer\Id;
use AmeliaBooking\Domain\ValueObjects\DateTime\DateTimeValue;
use AmeliaBooking\Domain\ValueObjects\Number\Integer\DateRepeat;
use AmeliaBooking\Domain\ValueObjects\String\DayOffType;
use AmeliaBooking\Domain\ValueObjects\String\Name;

class BlockTime
{
    /** @var Id */
    private $id;

    /** @var Name */
    private $name;

    /** @var Id | null */
    private $userId;

    /** @var Provider | null */
    private $user;

    /** @var DateTimeValue */
    private $startDate;

    /** @var DateTimeValue */
    private $endDate;

    /** @var DayOffType */
    private $type;

    /** @var DateRepeat */
    private $repeat;

    /**
     * @param Name $name
     * @param Id | null $userId
     * @param DateTimeValue $startDate
     * @param DateTimeValue $endDate
     */
    public function __construct(
        Name $name,
        ?Id $userId,
        DateTimeValue $startDate,
        DateTimeValue $endDate
    ) {
        $this->name      = $name;
        $this->userId    = $userId;
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
        $this->type      = new DayOffType(DayOffType::BLOCK_TIME);
        $this->repeat    = new DateRepeat(0);
    }

    /**
     * @return Id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Id $id
     */
    public function setId(Id $id)
    {
        $this->id = $id;
    }

    /**
     * @return Id | null
     */
    public function getUserId(): ?Id
    {
        return $this->userId;
    }

    /**
     * @param Id|null $userId
     */
    public function setUserId(?Id $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return Provider | null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param Provider | null $user
     */
    public function setUser(?Provider $user)
    {
        $this->user = $user;
    }

    /**
     * @return DateTimeValue
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param DateTimeValue $startDate
     */
    public function setStartDate(DateTimeValue $startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return DateTimeValue
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param DateTimeValue $endDate
     */
    public function setEndDate(DateTimeValue $endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Name $name
     */
    public function setName(Name $name)
    {
        $this->name = $name;
    }

    /**
     * @return DayOffType
     */
    public function getType(): DayOffType
    {
        return $this->type;
    }

    /**
     * @param DayOffType $type
     */
    public function setType(DayOffType $type)
    {
        $this->type = $type;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'        => null !== $this->id ? $this->id->getValue() : null,
            'name'      => $this->name->getValue(),
            'userId'    => null !== $this->userId ? $this->userId->getValue() : null,
            'user'      => null !== $this->getUser() ? $this->getUser()->toArray() : null,
            'startDate' => $this->startDate->getValue()->format('Y-m-d H:i:s'),
            'endDate'   => $this->endDate->getValue()->format('Y-m-d H:i:s'),
            'repeat'    => $this->repeat->getValue(),
            'type'      => $this->type->getValue(),
        ];
    }
}
