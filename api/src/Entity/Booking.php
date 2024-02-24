<?php

namespace App\Entity;

use App\Exception\BookingEndsBeforeStartingException;
use App\Exception\BookingInThePastException;
use Symfony\Component\Clock\DatePoint;

readonly class Booking
{
    public DatePoint $startDate;
    public DatePoint $endDate;

    /**
     * @throws BookingInThePastException Bookings can't be made for past dates.
     * @throws BookingEndsBeforeStartingException Start date must be before end date.
     */
    public function __construct(DatePoint $startDate, DatePoint $endDate)
    {
        $now = new DatePoint();
        if ($startDate < $now) {
            throw new BookingInThePastException();
        }
        if ($endDate < $startDate) {
            throw new BookingEndsBeforeStartingException();
        }

        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
}
