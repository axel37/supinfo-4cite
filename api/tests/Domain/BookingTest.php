<?php

namespace App\Tests\Domain;

use App\Entity\Booking;
use App\Entity\Hotel;
use App\Entity\Room;
use App\Exception\BookingEndsBeforeStartingException;
use App\Exception\BookingInThePastException;
use App\Exception\BookingStartsAndEndsOnSameDayException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\DatePoint;

class BookingTest extends TestCase
{
    private Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        $this->room = new Room(new Hotel('Grand hotel', 'Pine street'), 'Room 237');
    }

    public function testCanReadDatesAfterCreation(): void
    {
        $startDate = new DatePoint('today');
        $endDate = new DatePoint('tomorrow');

        $booking = new Booking($this->room, $startDate, $endDate);

        $this->assertEquals($startDate, $booking->getStart());
        $this->assertEquals($endDate, $booking->getEnd());
    }

    public function testBookingPastDateFails(): void
    {
        $startDate = new DatePoint('yesterday');
        $endDate = new DatePoint('tomorrow');

        $this->expectException(BookingInThePastException::class);
        $booking = new Booking($this->room, $startDate, $endDate);
    }

    public function testFailWhenEndBeforeStart(): void
    {
        $startDate = new DatePoint('tomorrow + 1 day');
        $endDate = new DatePoint('tomorrow');

        $this->expectException(BookingEndsBeforeStartingException::class);
        $booking = new Booking($this->room, $startDate, $endDate);
    }

    public function testFailWhenStartAndEndOnSameDay(): void
    {
        $startDate = new DatePoint('tomorrow');
        $endDate = new DatePoint('tomorrow + 6 hours');

        $this->expectException(BookingStartsAndEndsOnSameDayException::class);
        $booking = new Booking($this->room, $startDate, $endDate);
    }
}
