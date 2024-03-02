<?php

namespace App\Tests\Entity;

use App\Entity\Room;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RoomTest extends KernelTestCase
{


    private ContainerInterface $container;
    private EntityManagerInterface $em;
    private RoomRepository $roomRepository;

    protected function setUp(): void
    {
        parent::setUp();
        // Note : KernelTestCase shuts down the kernel between each test already
        self::bootKernel();
        $this->container = static::getContainer();
        $this->em = $this->container->get('doctrine')->getManager();
        $this->roomRepository = $this->container->get(RoomRepository::class);
    }
    public function testCanCreateRoom(): void
    {

        $room = new Room('Room 237');
        $id = $room->getId();

        $this->em->persist($room);
        $this->em->flush();

        $roomFromDatabase = $this->roomRepository->find($id);

        $this->assertNotNull($roomFromDatabase);
        $this->assertEquals('Room 237', $roomFromDatabase->getName());
    }
}
