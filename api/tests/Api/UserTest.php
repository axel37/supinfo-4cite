<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Hotel;
use App\Entity\Room;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserTest extends ApiTestCase
{
    private ContainerInterface $container;
    private EntityManagerInterface $em;
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $hasher;
    protected function setUp(): void
    {
        parent::setUp();
        $this->container = self::getContainer();
        $this->em = $this->container->get('doctrine')->getManager();
        $this->userRepository = $this->container->get(UserRepository::class);
        $this->hasher = $this->container->get('security.user_password_hasher');
    }


    public function testCanCreateUser(): void
    {
        static::createClient()->request('POST', '/users', [
            'json' => [
                'email' => 'user@example.com',
                'userName' => 'UserName',
                'plainPassword' => 'password'
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        self::assertResponseStatusCodeSame(201);
        self::assertJsonContains([
            '@context' => '/contexts/User',
            '@type' => 'User',
            'email' => 'user@example.com',
            'userName' => 'UserName',
        ]);
    }

    public function testCanCreateThenReadBooking(): void
    {
        $hotel = new Hotel('Testing hotel', 'Test street');
        $room = new Room($hotel, 'Testing room');
        $roomId = $room->getId();
        $hotel->addRoom($room);

        $user = new User('testing@example.com', 'TestingUser');
        $user->setPassword('password');
        $userId = $user->getId();
        $this->em->persist($user);
        $this->em->persist($hotel);
        $this->em->flush();

        $start = new DatePoint('today');
        $end = new DatePoint('tomorrow');
        static::createClient()->request('POST', '/bookings', [
            'json' => [
                'ownerId' => $userId,
                'roomId' => $roomId,
                'startDate' => $start->format('Y-m-d'),
                'endDate' => $end->format('Y-m-d')
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);
        self::assertResponseIsSuccessful();

        $response = self::createClient()->request('GET', '/users/' . $userId);
        self::assertJsonContains([
                '@context' => '/contexts/User',
                '@type' => 'User',
                'email' => 'testing@example.com',
                'userName' => 'TestingUser',
                'bookingIds' => []
            ]);
        // get bookingIds array from response and assert that its count is 1
        $responseData = json_decode($response->getContent(), true);
        $bookingIds = $responseData['bookingIds'];
        self::assertCount(1, $bookingIds);
    }

    public function disabled_testLogin(): void
    {
        $client = self::createClient();

        $user = new User('testing@example.com', 'Testing User');
        $user->setPassword(
            $this->hasher->hashPassword($user, '$3CR3T')
        );

        $this->em->persist($user);
        $this->em->flush();

        // retrieve a token
        $response = $client->request('POST', '/auth', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'testing@example.com',
                'password' => '$3CR3T',
            ],
        ]);

        $json = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);


        // test not authorized
        $client->request('GET', '/hotels');
        $this->assertResponseStatusCodeSame(401);

        // test authorized
        $client->request('GET', '/hotels', ['auth_bearer' => $json['token']]);
        $this->assertResponseIsSuccessful();
    }

}
