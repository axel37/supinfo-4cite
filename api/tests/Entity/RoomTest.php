<?php

namespace App\Tests\Entity;

use App\Entity\Hotel;
use App\Entity\Room;
use App\Repository\BookingRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Uid\Uuid;

class RoomTest extends KernelTestCase
{
    private ContainerInterface $container;
    private EntityManagerInterface $em;
    private RoomRepository $roomRepository;
    private Room $room;
    private Uuid $roomId;
    private BookingRepository $bookingRepository;
    private Hotel $hotel;

    protected function setUp(): void
    {
        parent::setUp();
        // Note : KernelTestCase shuts down the kernel between each test already
        self::bootKernel();
        $this->container = static::getContainer();
        $this->em = $this->container->get('doctrine')->getManager();
        $this->roomRepository = $this->container->get(RoomRepository::class);
        $this->bookingRepository = $this->container->get(BookingRepository::class);

        $this->hotel = new Hotel('Grand hotel', 'Pine street');
        $this->room = new Room($this->hotel,'Room 237');
        $this->roomId = $this->room->getId();
        $this->em->persist($this->hotel);
        $this->em->persist($this->room);
        $this->em->flush();
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->em->close();
        unset($this->em);
    }

    public function testCanCreateRoom(): void
    {
        $roomFromDatabase = $this->roomRepository->find($this->roomId);

        $this->assertNotNull($roomFromDatabase);
        $this->assertEquals('Room 237', $roomFromDatabase->getName());
    }

    public function testCanGetBookings(): void
    {
        $roomFromDatabase = $this->roomRepository->find($this->roomId);
        $roomFromDatabase->book(new DatePoint('today'), new DatePoint('tomorrow'));

        $this->em->flush();

        $roomFromDatabase = $this->roomRepository->find($this->roomId);
        $this->assertCount(1, $roomFromDatabase->getBookings());
    }

    public function testDeleteRoomCascadesBookings()
    {
        $roomToDelete = new Room($this->hotel,'Room to be deleted');
        $roomId = $roomToDelete->getId();
        $roomToDelete->book(new DatePoint('today'), new DatePoint('tomorrow'));
        $bookingId = $roomToDelete->getBookings()[0]->getId();

        $this->em->persist($roomToDelete);
        $this->em->flush();

        $roomFromDatabase = $this->roomRepository->find($roomId);
        $this->assertCount(1, $roomFromDatabase->getBookings());

        $this->em->remove($roomFromDatabase);
        $this->em->flush();

        $roomFromDatabase = $this->roomRepository->find($roomId);
        $bookingFromDatabase = $this->bookingRepository->find($bookingId);
        $this->assertNull($bookingFromDatabase);
    }
}
