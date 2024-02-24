<?php

namespace App\Entity;

use App\Exception\BookingEndsBeforeStartingException;
use App\Exception\BookingInThePastException;
use App\Exception\BookingStartsAndEndsOnSameDayException;
use App\Exception\RoomUnavailableForBookingException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Clock\DatePoint;

class Room
{
    /** @var Collection<Booking> */
    private Collection $bookings;
    public function __construct()
    {
        $this->bookings = new ArrayCollection();
    }

    /**
     * @throws RoomUnavailableForBookingException The new booking overlaps with one of the room's bookings.
     * @throws BookingInThePastException
     * @throws BookingStartsAndEndsOnSameDayException
     * @throws BookingEndsBeforeStartingException
     */
    public function book(DatePoint $startDate, DatePoint $endDate): void
    {
        $booking = new Booking($startDate, $endDate);
        if ($this->hasBookingAtDates($startDate, $endDate)) {
            throw new RoomUnavailableForBookingException();
        }
        $this->bookings->add($booking);
    }

    private function hasBookingAtDates(DatePoint $startDate, DatePoint $endDate): bool
    {
        return $this->bookings->exists(
            fn(int $index, Booking $booking) => $booking->startDate < $endDate && $booking->endDate > $startDate
        );
    }

    /**
     * Returns a copy of this room's bookings.
     *
     * @returns Collection<Booking>
     */
    public function getBookings(): Collection
    {
        return new ArrayCollection($this->bookings->toArray());
    }
}
