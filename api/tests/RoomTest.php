<?php

namespace App\Tests;

use App\Entity\Room;
use App\Exception\EmptyNameException;
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

    private Room $room;

    protected function setUp(): void
    {
        parent::setUp();
        $this->room = new Room('Room 237');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->room);
    }

    public function testCanReadName(): void
    {
        $room = new Room('Room 237');
        $this->assertEquals('Room 237', $room->getName());
    }

    public function testFailOnInvalidName(): void
    {
        $this->expectException(EmptyNameException::class);
        $room = new Room(' ');
    }

    public function testCanUpdateName(): void
    {
        $this->room->setName('Presidential Suite');
        $this->assertEquals('Presidential Suite', $this->room->getName());
    }

    public function testFailsOnUpdateInvalidName(): void
    {
        $this->expectException(EmptyNameException::class);
        $this->room->setName(' ');
    }

    public function testCanStartOrEndOnSameDay(): void
    {
        $today = new DatePoint('today');
        $tomorrow = new DatePoint('tomorrow');
        $dayAfterTomorrow = new DatePoint('tomorrow + 1 day');

        $this->room->book($today, $tomorrow);
        $this->room->book($tomorrow, $dayAfterTomorrow);

        $this->assertCount(2, $this->room->getBookings());
    }

    public function testUnavailableStartsBeforeEndsDuring(): void
    {
        $validStartDate = new DatePoint('tomorrow');
        $validEndDate = new DatePoint('tomorrow + 3 days');
        $this->room->book($validStartDate, $validEndDate);

        $invalidStart1 = new DatePoint('today');
        $invalidEnd1 = new DatePoint('today + 3 days');
        $this->expectException(RoomUnavailableForBookingException::class);
        $this->room->book($invalidStart1, $invalidEnd1);
    }
    public function testUnavailableStartsBeforeEndsAfter(): void
    {
        $validStartDate = new DatePoint('tomorrow');
        $validEndDate = new DatePoint('tomorrow + 3 days');
        $this->room->book($validStartDate, $validEndDate);

        $invalidStart1 = new DatePoint('today');
        $invalidEnd1 = new DatePoint('tomorrow + 10 days');
        $this->expectException(RoomUnavailableForBookingException::class);
        $this->room->book($invalidStart1, $invalidEnd1);
    }
    public function testUnavailableStartsDuringEndsAfter(): void
    {
        $validStartDate = new DatePoint('tomorrow');
        $validEndDate = new DatePoint('tomorrow + 3 days');
        $this->room->book($validStartDate, $validEndDate);

        $invalidStart1 = new DatePoint('tomorrow + 1 day');
        $invalidEnd1 = new DatePoint('tomorrow + 10 days');
        $this->expectException(RoomUnavailableForBookingException::class);
        $this->room->book($invalidStart1, $invalidEnd1);
    }
    public function testUnavailableStartsDuringEndsDuring(): void
    {
        $validStartDate = new DatePoint('tomorrow');
        $validEndDate = new DatePoint('tomorrow + 3 days');
        $this->room->book($validStartDate, $validEndDate);

        $invalidStart1 = new DatePoint('tomorrow + 1 day');
        $invalidEnd1 = new DatePoint('tomorrow + 2 days');
        $this->expectException(RoomUnavailableForBookingException::class);
        $this->room->book($invalidStart1, $invalidEnd1);
    }
    public function testUnavailableStartsSameDayEndsDuring(): void
    {
        $validStartDate = new DatePoint('tomorrow');
        $validEndDate = new DatePoint('tomorrow + 3 days');
        $this->room->book($validStartDate, $validEndDate);

        $invalidStart1 = new DatePoint('tomorrow');
        $invalidEnd1 = new DatePoint('tomorrow + 2 days');
        $this->expectException(RoomUnavailableForBookingException::class);
        $this->room->book($invalidStart1, $invalidEnd1);
    }
    public function testUnavailableStartsDuringEndsSameDay(): void
    {
        $validStartDate = new DatePoint('tomorrow');
        $validEndDate = new DatePoint('tomorrow + 3 days');
        $this->room->book($validStartDate, $validEndDate);

        $invalidStart1 = new DatePoint('tomorrow + 1 day');
        $invalidEnd1 = new DatePoint('tomorrow + 3 days');
        $this->expectException(RoomUnavailableForBookingException::class);
        $this->room->book($invalidStart1, $invalidEnd1);
    }
}
