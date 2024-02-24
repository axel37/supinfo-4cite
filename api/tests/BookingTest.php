<?php

namespace App\Tests;

use App\Entity\Booking;
use App\Exception\BookingEndsBeforeStartingException;
use App\Exception\BookingInThePastException;
use App\Exception\BookingStartsAndEndsOnSameDayException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\DatePoint;

class BookingTest extends TestCase
{
    public function testCanReadDatesAfterCreation(): void
    {
        $startDate = new DatePoint('today');
        $endDate = new DatePoint('tomorrow');

        $booking = new Booking($startDate, $endDate);

        $this->assertEquals($startDate, $booking->startDate);
        $this->assertEquals($endDate, $booking->endDate);
    }

    public function testBookingPastDateFails(): void
    {
        $startDate = new DatePoint('yesterday');
        $endDate = new DatePoint('tomorrow');

        $this->expectException(BookingInThePastException::class);
        $booking = new Booking($startDate, $endDate);
    }

    public function testFailWhenEndBeforeStart(): void
    {
        $startDate = new DatePoint('tomorrow + 1 day');
        $endDate = new DatePoint('tomorrow');

        $this->expectException(BookingEndsBeforeStartingException::class);
        $booking = new Booking($startDate, $endDate);
    }

    public function testFailWhenStartAndEndOnSameDay(): void
    {
        $startDate = new DatePoint('tomorrow');
        $endDate = new DatePoint('tomorrow + 6 hours');

        $this->expectException(BookingStartsAndEndsOnSameDayException::class);
        $booking = new Booking($startDate, $endDate);
    }
}
