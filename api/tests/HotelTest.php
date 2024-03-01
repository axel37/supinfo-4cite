<?php

namespace App\Tests;

use App\Entity\Hotel;
use App\Entity\Room;
use PHPUnit\Framework\TestCase;

class HotelTest extends TestCase
{
    // TODO : SetUp() : create some rooms ?
    public function testCanSeeRooms(): void
    {
        $roomA = new Room();
        $roomB = new Room();
        $roomC = new Room();
        $rooms = [$roomA, $roomB, $roomC];

        $hotel = new Hotel($rooms);

        $this->assertCount(3, $hotel->getRooms());
        $this->assertContains($roomA, $hotel->getRooms());
        $this->assertContains($roomB, $hotel->getRooms());
        $this->assertContains($roomC, $hotel->getRooms());
    }
}
