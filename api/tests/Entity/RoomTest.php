<?php

namespace App\Tests\Entity;

use App\Entity\Room;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\DependencyInjection\ContainerInterface;

use function PHPUnit\Framework\assertEquals;

class RoomTest extends KernelTestCase
{
    private ContainerInterface $container;
    private EntityManagerInterface $em;
    private RoomRepository $roomRepository;
    private $room;
    private $roomId;

    protected function setUp(): void
    {
        parent::setUp();
        // Note : KernelTestCase shuts down the kernel between each test already
        self::bootKernel();
        $this->container = static::getContainer();
        $this->em = $this->container->get('doctrine')->getManager();
        $this->roomRepository = $this->container->get(RoomRepository::class);

        $this->room = new Room('Room 237');
        $this->roomId = $this->room->getId();
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
}
