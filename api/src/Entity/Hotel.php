<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Exception\EmptyLocationException;
use App\Exception\EmptyNameException;
use App\Exception\RoomAlreadyInHotelException;
use App\Hotel\BookableInterface;
use App\Hotel\BookingInterface;

class Hotel
{
    /** @var Picture[] $pictures */
    private array $pictures = [];

    /**
     * @param Room[] $rooms
     */
    public function __construct(private string $name, private string $location, /** @var iterable<BookableInterface> $rooms */ private iterable $rooms = [], private ?string $description = null)
    {
        $this->setName($name);
        $this->setLocation($location);
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPictures(): array
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): void
    {
        $this->pictures[] = $picture;
    }

    public function removePicture(Picture $picture): void
    {
        $this->pictures = array_udiff($this->pictures, [$picture], static fn(Picture $a, Picture $b) => $a <=> $b);
    }

    public function setName(string $name): void
    {
        if (trim($name) === '') {
            throw new EmptyNameException();
        }
        $this->name = $name;
    }

    public function setLocation(string $location): void
    {
        if (trim($location) === '') {
            throw new EmptyLocationException();
        }
        $this->location = $location;
    }

    public function removeRoom(Room $room): void
    {
        $this->rooms = array_udiff($this->rooms, [$room], static fn(Room $a, Room $b) => $a <=> $b);
    }

    public function addRoom(Room $room): void
    {
        if (in_array($room, $this->rooms)) {
            throw new RoomAlreadyInHotelException();
        }
        $this->rooms[] = $room;
    }
}
