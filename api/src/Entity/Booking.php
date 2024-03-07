<?php

namespace App\Entity;

use App\Exception\BookingEndsBeforeStartingException;
use App\Exception\BookingInThePastException;
use App\Exception\BookingStartsAndEndsOnSameDayException;
use App\Hotel\BookingInterface;
use App\Repository\BookingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\Uid\Uuid;

#[Entity(repositoryClass: BookingRepository::class)]
readonly class Booking implements BookingInterface
{
    #[Column(type: UuidType::NAME)]
    #[Id]
    private Uuid $id;
    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeInterface $startDate;
    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeInterface $endDate;

    #[ManyToOne(targetEntity: Room::class, inversedBy: 'bookings')]
    private Room $room;

    #[ManyToOne(targetEntity: User::class, inversedBy: 'bookings')]
    private User $owner;

    /**
     * @throws BookingInThePastException Bookings can't be made for past dates.
     * @throws BookingEndsBeforeStartingException Start date must be before end date.
     * @throws BookingStartsAndEndsOnSameDayException Start and end must be on different days.
     */
    public function __construct(Room $room, \DateTimeInterface $startDate, \DateTimeInterface $endDate)
    {
        $this->id = Uuid::v4();

        $today = new DatePoint('today');
        if ($startDate < $today) {
            throw new BookingInThePastException();
        }
        if ($endDate < $startDate) {
            throw new BookingEndsBeforeStartingException();
        }
        if ($startDate->format('Y-m-d') === $endDate->format('Y-m-d')) {
            throw new BookingStartsAndEndsOnSameDayException();
        }

        $this->startDate = $startDate;
        $this->endDate = $endDate;

        $this->room = $room;
    }

    public function getStart(): \DateTimeInterface
    {
        return $this->startDate;
    }

    public function getEnd(): \DateTimeInterface
    {
        return $this->endDate;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getRoom(): Room
    {
        return $this->room;
    }

    public function setOwner(User $owner): void
    {
        $this->owner = $owner;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }
}
