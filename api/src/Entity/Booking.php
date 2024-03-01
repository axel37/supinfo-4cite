<?php

namespace App\Entity;

use App\Exception\BookingEndsBeforeStartingException;
use App\Exception\BookingInThePastException;
use App\Exception\BookingStartsAndEndsOnSameDayException;
use App\Hotel\BookingInterface;
use Symfony\Component\Clock\DatePoint;

readonly class Booking implements BookingInterface
{
    public \DateTimeInterface $startDate;
    public \DateTimeInterface $endDate;

    /**
     * @throws BookingInThePastException Bookings can't be made for past dates.
     * @throws BookingEndsBeforeStartingException Start date must be before end date.
     * @throws BookingStartsAndEndsOnSameDayException Start and end must be on different days.
     */
    public function __construct(\DateTimeInterface $startDate, \DateTimeInterface $endDate)
    {
        $today = new DatePoint('today');
        if ($startDate < $today) {
            throw new BookingInThePastException();
        }
        if ($endDate < $startDate) {
            throw new BookingEndsBeforeStartingException();
        }
        if ($startDate->format('Y-m-d') === $endDate->format('Y-m-d')) {
            throw new BookingStartsAndEndsOnSameDayException();
        }

        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function getStart(): \DateTimeInterface
    {
        return $this->startDate;
    }

    public function getEnd(): \DateTimeInterface
    {
        return $this->endDate;
    }
}
