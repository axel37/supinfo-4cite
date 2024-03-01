<?php

namespace App\Tests;

use App\Entity\Room;
use App\Exception\RoomUnavailableForBookingException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\DatePoint;

class RoomTest extends TestCase
{
    /*
     * Note about terminology used in these tests :
     * - "Before" = Before the start of booking
     * - "After" = After the end of booking
     * - "During" = In the middle of the booking
     * - "SameDay" = On the same day as booking start/end
     */

    public function testCanStartOrEndOnSameDay()
    {
        $room = new Room();

        $today = new DatePoint('today');
        $tomorrow = new DatePoint('tomorrow');
        $dayAfterTomorrow = new DatePoint('tomorrow + 1 day');

        $room->book($today, $tomorrow);
        $room->book($tomorrow, $dayAfterTomorrow);

        $this->assertCount(2, $room->getBookings());
    }

    public function testUnavailableStartsBeforeEndsDuring(): void
    {
        $room = new Room();

        $validStartDate = new DatePoint('tomorrow');
        $validEndDate = new DatePoint('tomorrow + 3 days');
        $room->book($validStartDate, $validEndDate);

        $invalidStart1 = new DatePoint('today');
        $invalidEnd1 = new DatePoint('today + 3 days');
        $this->expectException(RoomUnavailableForBookingException::class);
        $room->book($invalidStart1, $invalidEnd1);
    }
    public function testUnavailableStartsBeforeEndsAfter(): void
    {
        $room = new Room();

        $validStartDate = new DatePoint('tomorrow');
        $validEndDate = new DatePoint('tomorrow + 3 days');
        $room->book($validStartDate, $validEndDate);

        $invalidStart1 = new DatePoint('today');
        $invalidEnd1 = new DatePoint('tomorrow + 10 days');
        $this->expectException(RoomUnavailableForBookingException::class);
        $room->book($invalidStart1, $invalidEnd1);
    }
    public function testUnavailableStartsDuringEndsAfter(): void
    {
        $room = new Room();

        $validStartDate = new DatePoint('tomorrow');
        $validEndDate = new DatePoint('tomorrow + 3 days');
        $room->book($validStartDate, $validEndDate);

        $invalidStart1 = new DatePoint('tomorrow + 1 day');
        $invalidEnd1 = new DatePoint('tomorrow + 10 days');
        $this->expectException(RoomUnavailableForBookingException::class);
        $room->book($invalidStart1, $invalidEnd1);
    }
    public function testUnavailableStartsDuringEndsDuring(): void
    {
        $room = new Room();

        $validStartDate = new DatePoint('tomorrow');
        $validEndDate = new DatePoint('tomorrow + 3 days');
        $room->book($validStartDate, $validEndDate);

        $invalidStart1 = new DatePoint('tomorrow + 1 day');
        $invalidEnd1 = new DatePoint('tomorrow + 2 days');
        $this->expectException(RoomUnavailableForBookingException::class);
        $room->book($invalidStart1, $invalidEnd1);
    }
    public function testUnavailableStartsSameDayEndsDuring(): void
    {
        $room = new Room();

        $validStartDate = new DatePoint('tomorrow');
        $validEndDate = new DatePoint('tomorrow + 3 days');
        $room->book($validStartDate, $validEndDate);

        $invalidStart1 = new DatePoint('tomorrow');
        $invalidEnd1 = new DatePoint('tomorrow + 2 days');
        $this->expectException(RoomUnavailableForBookingException::class);
        $room->book($invalidStart1, $invalidEnd1);
    }
    public function testUnavailableStartsDuringEndsSameDay(): void
    {
        $room = new Room();

        $validStartDate = new DatePoint('tomorrow');
        $validEndDate = new DatePoint('tomorrow + 3 days');
        $room->book($validStartDate, $validEndDate);

        $invalidStart1 = new DatePoint('tomorrow + 1 day');
        $invalidEnd1 = new DatePoint('tomorrow + 3 days');
        $this->expectException(RoomUnavailableForBookingException::class);
        $room->book($invalidStart1, $invalidEnd1);
    }
}
