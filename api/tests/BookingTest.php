<?php

namespace App\Tests;

use App\Entity\Booking;
use App\Exception\BookingInThePastException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\DatePoint;

class BookingTest extends TestCase
{
    public function testBookingPastDateFails(): void
    {
        $startDate = new DatePoint('yesterday');
        $endDate = new DatePoint('tomorrow');

        $this->expectException(BookingInThePastException::class);
        $booking = new Booking($startDate, $endDate);
    }
}
