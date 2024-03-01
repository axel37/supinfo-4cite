<?php

namespace App\Tests;

use App\Entity\Hotel;
use App\Entity\Room;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\DatePoint;

class HotelTest extends TestCase
{
    private $roomA;
    private $roomB;
    private $roomC;
    private $rooms;
    private $hotel;

    /**
     * Create some Rooms and a Hotel to be used in subsequent tests.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->roomA = new Room();
        $this->roomB = new Room();
        $this->roomC = new Room();
        $this->rooms = [$this->roomA, $this->roomB, $this->roomC];
        $this->hotel = new Hotel($this->rooms);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->hotel, $this->rooms, $this->roomC, $this->roomB, $this->roomA);
    }

    public function testCanSeeRooms(): void
    {
        $this->assertCount(3, $this->hotel->getRooms());
        $this->assertContains($this->roomA, $this->hotel->getRooms());
        $this->assertContains($this->roomB, $this->hotel->getRooms());
        $this->assertContains($this->roomC, $this->hotel->getRooms());
    }

    public function testCanSeeBookings(): void
    {
        $this->roomA->book(new DatePoint('today'), new DatePoint('tomorrow'));
        $this->roomB->book(new DatePoint('today'), new DatePoint('tomorrow'));
        $this->roomC->book(new DatePoint('today'), new DatePoint('tomorrow'));
        $this->roomC->book(new DatePoint('tomorrow'), new DatePoint('tomorrow + 2 days'));

        $this->assertCount(4, $this->hotel->getBookings());
    }
}
