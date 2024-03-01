<?php

namespace App\Entity;

use App\Exception\BookingEndsBeforeStartingException;
use App\Exception\BookingInThePastException;
use App\Exception\BookingStartsAndEndsOnSameDayException;
use App\Exception\EmptyNameException;
use App\Exception\RoomUnavailableForBookingException;
use App\Hotel\BookableInterface;
use App\Hotel\BookingInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Clock\DatePoint;

class Room implements BookableInterface
{
    /** @var Collection<Booking> */
    private Collection $bookings;
    public function __construct(private string $name)
    {
        if (trim($this->name) === '') {
            throw new EmptyNameException();
        }

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
        $booking = new Booking($startDate, $endDate);
        if ($this->hasBookingAtDates($startDate, $endDate)) {
            throw new RoomUnavailableForBookingException();
        }
        $this->bookings->add($booking);
        return $booking;
    }

    private function hasBookingAtDates(\DateTimeInterface $startDate, \DateTimeInterface $endDate): bool
    {
        return $this->bookings->exists(
            fn(int $index, Booking $booking) => $booking->startDate < $endDate && $booking->endDate > $startDate
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
        $this->name = $name;
    }
}
