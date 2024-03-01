<?php

namespace App\Entity;

use App\Hotel\BookableInterface;

class Hotel
{

    /**
     * @param Room[] $rooms
     */
    public function __construct(/** @var array<BookableInterface> $rooms Hello */ private readonly array $rooms)
    {
    }

    public function getRooms(): array
    {
        return $this->rooms;
    }
}
