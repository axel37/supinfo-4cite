<?php

namespace App\Entity;

use App\Exception\EmptyEmailException;
use App\Exception\EmptyNameException;
use App\Hotel\BookingInterface;

class User
{
    /** @var BookingInterface[] */
    private array $bookings = [];

    public function __construct(private string $email, private string $username)
    {
        $this->setEmail($this->email);
        if (trim($this->username) === '') {
            throw new EmptyNameException();
        }
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function book(Room $room, \DateTimeInterface $start, \DateTimeInterface $end): void
    {
        $this->bookings[] = $room->book($start, $end);
    }

    public function getBookings(): array
    {
        return $this->bookings;
    }

    public function setEmail(string $email): void
    {
        if (trim($email) === '') {
            throw new EmptyEmailException();
        }
        $this->email = $email;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }
}
