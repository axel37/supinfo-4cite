<?php

namespace App\Entity;

use App\Exception\EmptyLocationException;
use App\Exception\EmptyNameException;
use App\Hotel\BookableInterface;
use App\Hotel\BookingInterface;

class Hotel
{

    /**
     * @param Room[] $rooms
     */
    public function __construct(private string $name, private string $location, /** @var iterable<BookableInterface> $rooms Hello */ private readonly iterable $rooms)
    {
        if (trim($this->name) === '') {
            throw new EmptyNameException();
        }
        if (trim($this->location) === '') {
            throw new EmptyLocationException();
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLocation(): string
    {
        return $this->location;
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
