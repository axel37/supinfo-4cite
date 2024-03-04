<?php

namespace App\Tests\Entity;

use App\Entity\Hotel;
use App\Entity\Room;
use App\Repository\HotelRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Uid\Uuid;

class HotelTest extends KernelTestCase
{
    private ContainerInterface $container;
    private EntityManagerInterface $em;
    private HotelRepository $hotelRepository;
    private Uuid $hotelId;
    private RoomRepository $roomRepository;

    protected function setUp(): void
    {
        parent::setUp();
        // Note : KernelTestCase shuts down the kernel between each test already
        self::bootKernel();
        $this->container = static::getContainer();
        $this->em = $this->container->get('doctrine')->getManager();
        $this->hotelRepository = $this->container->get(HotelRepository::class);
        $this->roomRepository = $this->container->get(RoomRepository::class);

        $hotel = new Hotel('Grand Hotel', 'Pine Street');
        $this->hotelId = $hotel->getId();
        $this->em->persist($hotel);
        $this->em->flush();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->em->close();
        unset($this->em);
    }

    public function testCanCreateHotel(): void
    {
        $hotel = $this->hotelRepository->find($this->hotelId);
        $this->assertNotNull($hotel);
        $this->assertEquals('Grand Hotel', $hotel->getName());
        $this->assertEquals('Pine Street', $hotel->getLocation());
    }

    public function testCanAddAndRemoveRooms(): void
    {
        $room = new Room('Room 237');
        $roomId = $room->getId();
        $hotel = $this->hotelRepository->find($this->hotelId);
        $hotel->addRoom($room);
        $this->em->flush();

        $hotel = $this->hotelRepository->find($this->hotelId);
        $this->assertCount(1, $hotel->getRooms());
        $this->assertEquals($roomId, $hotel->getRooms()[0]->getId());

        $room = $this->roomRepository->find($roomId);
        $hotel->removeRoom($room);
        $this->em->flush();

        $hotel = $this->hotelRepository->find($this->hotelId);
        $this->assertCount(0, $hotel->getRooms());
    }

}
