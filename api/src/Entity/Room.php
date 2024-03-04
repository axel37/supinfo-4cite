<?php

namespace App\Entity;

use App\Exception\BookingEndsBeforeStartingException;
use App\Exception\BookingInThePastException;
use App\Exception\BookingStartsAndEndsOnSameDayException;
use App\Exception\EmptyNameException;
use App\Exception\RoomUnavailableForBookingException;
use App\Hotel\BookableInterface;
use App\Hotel\BookingInterface;
use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\Uid\Uuid;
use Doctrine\DBAL\Types\Types;

#[Entity(repositoryClass: RoomRepository::class)]
class Room implements BookableInterface
{
    #[Column(type: Types::GUID)]
    #[Id]
    private Uuid $id;

    #[OneToMany(mappedBy: 'room', targetEntity: Booking::class, cascade: ['persist', 'remove'])]
    /** @var Collection<Booking> */
    private Collection $bookings;
    #[Column]
    private string $name;

    private Hotel $hotel;
    public function __construct(string $name)
    {
        $this->id = Uuid::v4();
        $this->setName($name);
        $this->bookings = new ArrayCollection();
    }

    /**
     * @throws RoomUnavailableForBookingException The new booking overlaps with one of the room's bookings.
     * @throws BookingInThePastException
     * @throws BookingStartsAndEndsOnSameDayException
     * @throws BookingEndsBeforeStartingException
     */
    public function book(\DateTimeInterface $startDate, \DateTimeInterface $endDate): BookingInterface
    {
        $booking = new Booking($this, $startDate, $endDate);
        if ($this->hasBookingAtDates($startDate, $endDate)) {
            throw new RoomUnavailableForBookingException();
        }
        $this->bookings->add($booking);
        return $booking;
    }

    private function hasBookingAtDates(\DateTimeInterface $startDate, \DateTimeInterface $endDate): bool
    {
        return $this->bookings->exists(
            fn(int $index, Booking $booking) => $booking->getStart() < $endDate && $booking->getEnd() > $startDate
        );
    }

    /**
     * Returns a copy of this room's bookings.
     *
     * @returns iterable<Booking>
     */
    public function getBookings(): iterable
    {
        return new ArrayCollection($this->bookings->toArray());
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        if (trim($name) === '') {
            throw new EmptyNameException();
        }
        $this->name = $name;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }
}
