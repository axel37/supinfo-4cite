<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Hotel;
use App\Entity\Room;
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

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = self::getContainer();
        $this->em = $this->container->get('doctrine')->getManager();
        $this->bookingRepository = $this->container->get(BookingRepository::class);

        $hotel = new Hotel('Grand hotel', 'Pine street');
        $room = new Room($hotel, 'Room 237');
        $hotel->addRoom($room);
        $this->roomId = $room->getId();
        $this->em->persist($hotel);
        $this->em->flush();
    }

    public function testCanCreateBooking(): void
    {
        $start = new DatePoint('today');
        $end = new DatePoint('tomorrow');
        static::createClient()->request('POST', '/bookings', [
            'json' => [
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



}
