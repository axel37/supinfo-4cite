<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Hotel;
use App\Entity\Room;
use App\Entity\User;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Uid\Uuid;

class BookingTest extends ApiTestCase
{
    private ContainerInterface $container;
    private EntityManagerInterface $em;
    private BookingRepository $bookingRepository;
    private Uuid $roomId;
    private Uuid $userId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = self::getContainer();
        $this->em = $this->container->get('doctrine')->getManager();
        $this->bookingRepository = $this->container->get(BookingRepository::class);

        $hotel = new Hotel('Grand hotel', 'Pine street');
        $room = new Room($hotel, 'Room 237');
        $user = new User('testing@example.com', 'TestingUser');
        $user->setPassword('hello');
        $this->userId = $user->getId();
        $hotel->addRoom($room);
        $this->roomId = $room->getId();
        $this->em->persist($hotel);
        $this->em->persist($user);
        $this->em->flush();
    }

    public function testCanCreateBooking(): void
    {
        $start = new DatePoint('today');
        $end = new DatePoint('tomorrow');
        static::createClient()->request('POST', '/bookings', [
            'json' => [
                'ownerId' => $this->userId,
                'roomId' => $this->roomId,
                'startDate' => $start->format('Y-m-d'),
                'endDate' => $end->format('Y-m-d')
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        self::assertResponseStatusCodeSame(201);
        self::assertJsonContains([
            '@context' => '/contexts/Booking',
            '@type' => 'Booking',
            'roomId' => $this->roomId->toRfc4122(),
            'startDate' => $start->format('c'), // Ne va pas marcher
            'endDate' => $end->format('c')
        ]);

        // Check that there is now 1 booking
        static::createClient()->request('GET', '/bookings');
        self::assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/contexts/Booking',
            '@id' => '/bookings',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 1,
        ]);
    }

    public function testPutIsDisabled(): void
    {
        $start = new DatePoint('today');
        $end = new DatePoint('tomorrow');
        $response = static::createClient()->request('POST', '/bookings', [
            'json' => [
                'ownerId' => $this->userId,
                'roomId' => $this->roomId,
                'startDate' => $start->format('Y-m-d'),
                'endDate' => $end->format('Y-m-d')
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);
        // Get id of newly created room from response
        $data = json_decode($response->getContent(), true);
        $bookingId = $data['@id'];

        // Change the name of the newly created room through a PUT request
        $newStart = new DatePoint('today + 5 hours');
        static::createClient()->request('PUT', $bookingId, [
            'json' => [
                'startDate' => $newStart->format('Y-m-d'),
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);
        self::assertResponseStatusCodeSame(405);
    }

    public function testPatchIsDisabled(): void
    {
        $start = new DatePoint('today');
        $end = new DatePoint('tomorrow');
        $response = static::createClient()->request('POST', '/bookings', [
            'json' => [
                'ownerId' => $this->userId,
                'roomId' => $this->roomId,
                'startDate' => $start->format('Y-m-d'),
                'endDate' => $end->format('Y-m-d')
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);
        // Get id of newly created room from response
        $data = json_decode($response->getContent(), true);
        $bookingId = $data['@id'];

        // Change the name of the newly created room through a PUT request
        $newStart = new DatePoint('today + 5 hours');
        static::createClient()->request('PATCH', $bookingId, [
            'json' => [
                'startDate' => $newStart->format('Y-m-d'),
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);
        self::assertResponseStatusCodeSame(405);
    }

    public function testBookUnavailable(): void
    {
        $start = new DatePoint('today');
        $end = new DatePoint('tomorrow');
        static::createClient()->request('POST', '/bookings', [
            'json' => [
                'ownerId' => $this->userId,
                'roomId' => $this->roomId,
                'startDate' => $start->format('Y-m-d'),
                'endDate' => $end->format('Y-m-d')
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);
        static::createClient()->request('POST', '/bookings', [
            'json' => [
                'ownerId' => $this->userId,
                'roomId' => $this->roomId,
                'startDate' => $start->format('Y-m-d'),
                'endDate' => $end->format('Y-m-d')
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);
        self::assertResponseStatusCodeSame(409);
    }

    public function testDeleteBooking(): void
    {
        $start = new DatePoint('today');
        $end = new DatePoint('tomorrow');
        $response = static::createClient()->request('POST', '/bookings', [
            'json' => [
                'ownerId' => $this->userId,
                'roomId' => $this->roomId,
                'startDate' => $start->format('Y-m-d'),
                'endDate' => $end->format('Y-m-d')
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);
        $data = json_decode($response->getContent(), true);
        $bookingId = $data['@id'];

        $respones = static::createClient()->request('DELETE', $bookingId);
        self::assertResponseStatusCodeSame(204);

        // Check that there is now 1 booking
        static::createClient()->request('GET', '/bookings');
        self::assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/contexts/Booking',
            '@id' => '/bookings',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 0,
        ]);
    }


}
