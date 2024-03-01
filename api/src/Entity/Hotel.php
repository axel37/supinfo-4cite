<?php

namespace App\Entity;

use App\Hotel\BookableInterface;
use App\Hotel\BookingInterface;

class Hotel
{

    /**
     * @param Room[] $rooms
     */
    public function __construct(/** @var iterable<BookableInterface> $rooms Hello */ private readonly iterable $rooms)
    {
    }

    public function getRooms(): iterable
    {
        return $this->rooms;
    }

    public function getBookings(): iterable
    {
        /** @var iterable<BookingInterface> $bookings */
        $bookings = [];
        foreach ($this->rooms as $room) {
            foreach ($room->getBookings() as $booking) {
                $bookings[] = $booking;
            }
        }
        return $bookings;
    }
}
