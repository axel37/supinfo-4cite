<?php

namespace App\Entity;

use App\Exception\BookingInThePastException;
use Symfony\Component\Clock\DatePoint;

readonly class Booking
{
    /**
     * @throws BookingInThePastException Bookings can't be made for past dates.
     */
    public function __construct(DatePoint $startDate, DatePoint $endDate)
    {
        $now = new DatePoint();
        if ($startDate < $now) {
            throw new BookingInThePastException();
        }
    }
}
