<?php

namespace App\Tests\Domain;

use App\Entity\Hotel;
use App\Entity\Picture;
use App\Entity\Room;
use App\Exception\EmptyLocationException;
use App\Exception\EmptyNameException;
use App\Exception\RoomAlreadyInHotelException;
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
        $this->hotel = new Hotel('Grand Hotel', 'Pine Street');
        $this->roomA = new Room($this->hotel,'Room A');
        $this->roomB = new Room($this->hotel,'Room B');
        $this->roomC = new Room($this->hotel,'Room C');
        $this->hotel->addRoom($this->roomA);
        $this->hotel->addRoom($this->roomB);
        $this->hotel->addRoom($this->roomC);
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

    public function testCanUpdateNameAndLocationAndRooms(): void
    {
        $this->hotel->setName('Parkside Hotel');
        $this->assertEquals('Parkside Hotel', $this->hotel->getName());

        $this->hotel->setLocation('Evergreen Park');
        $this->assertEquals('Evergreen Park', $this->hotel->getLocation());

        $this->hotel->removeRoom($this->roomA);
        $this->assertCount(2, $this->hotel->getRooms());
        $this->assertNotContains($this->roomA, $this->hotel->getRooms());

        $newRoom = new Room($this->hotel,'Room 000');
        $this->hotel->addRoom($newRoom);
        $this->assertCount(3, $this->hotel->getRooms());
        $this->assertContains($newRoom, $this->hotel->getRooms());
    }

    public function testFailsOnUpdateInvalidName(): void
    {
        $this->expectException(EmptyNameException::class);
        $this->hotel->setName(' ');
    }

    public function testFailsOnUpdateInvalidLocation(): self
    {
        $this->expectException(EmptyLocationException::class);
        $this->hotel->setLocation(' ');
    }

    public function testFailsOnAddRoomAlreadyInHotel(): void
    {
        $this->expectException(RoomAlreadyInHotelException::class);
        $this->hotel->addRoom($this->roomA);
    }

    public function testCanCreateHotelWithoutRooms(): void
    {
        $hotel = new Hotel('Grand Hotel', 'Pine Street');
        $this->assertCount(0, $hotel->getRooms());
    }

    public function testCanCreateRoom()
    {
        $hotel = new Hotel('Parkside hotel', 'Oak Street');
        $room = $hotel->createRoom('Room 007');

        $this->assertEquals($hotel, $room->getHotel());
        $this->assertCount(1, $hotel->getRooms());
        $this->assertContains($room, $hotel->getRooms());
    }
}
