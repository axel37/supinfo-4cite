<?php

namespace App\Hotel;

/** Represents a booking, usually making the booked resource unavailable for this time period. */
interface BookingInterface
{
    public function getStart(): \DateTimeInterface;
    public function getEnd(): \DateTimeInterface;
}
