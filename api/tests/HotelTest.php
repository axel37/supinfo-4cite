<?php

namespace App\Tests;

use App\Entity\Hotel;
use App\Entity\Picture;
use App\Entity\Room;
use App\Exception\EmptyLocationException;
use App\Exception\EmptyNameException;
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
        $this->roomA = new Room('Room A');
        $this->roomB = new Room('Room B');
        $this->roomC = new Room('Room C');
        $this->rooms = [$this->roomA, $this->roomB, $this->roomC];
        $this->hotel = new Hotel('Grand Hotel', 'Pine Street', $this->rooms);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->hotel, $this->rooms, $this->roomC, $this->roomB, $this->roomA);
    }

    public function testCanReadNameAndLocationAndDescription(): void
    {
        $hotel = new Hotel('Grand Hotel', 'Pine Street', [], 'The greatest hotel in all the city.');
        $this->assertEquals('Grand Hotel', $hotel->getName());
        $this->assertEquals('Pine Street', $hotel->getLocation());
        $this->assertEquals('The greatest hotel in all the city.', $hotel->getDescription());
    }

    public function testAcceptsEmptyDescription(): void
    {
        $hotel = new Hotel('Grand Hotel', 'Pine Street', []);
        $this->assertNull($hotel->getDescription());
    }

    public function testFailsOnEmptyName(): void
    {
        $this->expectException(EmptyNameException::class);
        $hotel = new Hotel('  ', 'Pine Street', []);
    }

    public function testFailsOnEmptyLocation(): void
    {
        $this->expectException(EmptyLocationException::class);
        $hotel = new Hotel('Grand Hotel', '  ', []);
    }

    public function testCanAddAndRemovePictures(): void
    {
        $pictureA = new Picture('/pictures/a.jpg');
        $pictureB = new Picture('/pictures/B.jpg');

        $this->hotel->addPicture($pictureA);
        $this->hotel->addPicture($pictureB);

        $this->assertCount(2, $this->hotel->getPictures());
        $this->assertContains($pictureA, $this->hotel->getPictures());
        $this->assertContains($pictureB, $this->hotel->getPictures());

        $this->hotel->removePicture($pictureA);
        $this->assertCount(1, $this->hotel->getPictures());
        $this->assertNotContains($pictureA, $this->hotel->getPictures());
        $this->assertContains($pictureB, $this->hotel->getPictures());
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

    public function testCanUpdateName(): void
    {
        $this->hotel->setName('Parkside Hotel');
        self::assertEquals('Parkside Hotel', $this->hotel->getName());
    }
}
