<?php

namespace App\Tests\Entity;

use App\Entity\Hotel;
use App\Entity\Room;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserTest extends KernelTestCase
{
    private ContainerInterface $container;
    private EntityManagerInterface $em;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        // Note : KernelTestCase shuts down the kernel between each test already
        self::bootKernel();
        $this->container = static::getContainer();
        $this->em = $this->container->get('doctrine')->getManager();
        $this->userRepository = $this->container->get(UserRepository::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->em->close();
        unset($this->em);
    }

    public function testCanCreateUser(): void
    {
        $user = new User('user@example.com', 'NewUser');
        $user->setPassword('password');
        $id = $user->getId();
        $this->em->persist($user);
        $this->em->flush();

        $userFromDb = $this->userRepository->find($id);
        self::assertNotNull($userFromDb);
        self::assertEquals('user@example.com', $userFromDb->getEmail());
        self::assertEquals('NewUser', $userFromDb->getUsername());
    }

    public function testCanCreateBooking(): void
    {
        $hotel = new Hotel('Temp hotel', 'Pine street');
        $room = new Room($hotel, 'Temp Room');
        $this->em->persist($hotel);

        $user = new User('user@example.com', 'UserName');
        $id = $user->getId();
        $user->setPassword('password');
        $user->book($room, new DatePoint('today'), new DatePoint('tomorrow'));
        $this->em->persist($user);
        $this->em->flush();

        $userFromDb = $this->userRepository->find($id);
        self::assertCount(1, $userFromDb->getBookings());
    }

}
