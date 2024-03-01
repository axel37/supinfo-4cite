<?php

namespace App\Hotel;

/**
 * Represents an object that can be booked (from a start date to an end date), such as a hotel room.
 */
interface BookableInterface
{
    public function book(\DateTimeInterface $startDate, \DateTimeInterface $endDate): void;
}
