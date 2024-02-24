<?php

namespace App\Entity;

use App\Exception\BookingInThePastException;
use Symfony\Component\Clock\DatePoint;

readonly class Booking
{
    public DatePoint $startDate;
    public DatePoint $endDate;

    /**
     * @throws BookingInThePastException Bookings can't be made for past dates.
     */
    public function __construct(DatePoint $startDate, DatePoint $endDate)
    {
        $now = new DatePoint();
        if ($startDate < $now) {
            throw new BookingInThePastException();
        }

        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
}
