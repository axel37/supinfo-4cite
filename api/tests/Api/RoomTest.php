<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Hotel;
use App\Repository\HotelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Uid\Uuid;

class RoomTest extends ApiTestCase
{
    private ContainerInterface $container;
    private HotelRepository $hotelRepository;
    private EntityManagerInterface $em;
    private Uuid $hotelId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = self::getContainer();
        $this->em = $this->container->get('doctrine')->getManager();
        $this->hotelRepository = $this->container->get(HotelRepository::class);

        $hotel = new Hotel('Grand hotel', 'Pine street');
        $this->hotelId = $hotel->getId();
        $this->em->persist($hotel);
        $this->em->flush();
    }

    public function testCanCreateRoom(): void
    {
        static::createClient()->request('POST', '/rooms', [
            'json' => [
                'hotelId' => $this->hotelId,
                'name' => 'Room 237',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        self::assertResponseStatusCodeSame(201);
        self::assertJsonContains([
            '@context' => '/contexts/Room',
            '@type' => 'Room',
            'name' => 'Room 237',
        ]);
    }

    public function testFailsOnInvalidName(): void
    {
        static::createClient()->request('POST', '/rooms', [
            'json' => [
                'hotelId' => $this->hotelId,
                'name' => '',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);
        self::assertResponseStatusCodeSame(422);
    }

    public function testFailsOnNoHotelProvided(): void
    {
        static::createClient()->request('POST', '/rooms', [
            'json' => [
                'hotelId' => '',
                'name' => 'Room ABC',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);
        self::assertResponseStatusCodeSame(422);
    }

    public function testCreateThenFindRoom(): void
    {
        $response = static::createClient()->request('POST', '/rooms', [
            'json' => [
                'hotelId' => $this->hotelId,
                'name' => 'Room 237',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        // Get id of newly created room from response
        $data = json_decode($response->getContent(), true);
        $roomId = $data['@id'];

        // Check that there is now 1 room
        static::createClient()->request('GET', '/rooms');
        self::assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/contexts/Room',
            '@id' => '/rooms',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 1,
        ]);

        // Request the newly created room
        static::createClient()->request('GET', $roomId);
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/contexts/Room',
            '@type' => 'Room',
            '@id' => $roomId,
            'name' => 'Room 237',
        ]);
    }

    public function testPutIsDisabled()
    {
        $response = static::createClient()->request('POST', '/rooms', [
            'json' => [
                'hotelId' => $this->hotelId,
                'name' => 'Room 237',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);
        // Get id of newly created room from response
        $data = json_decode($response->getContent(), true);
        $roomId = $data['@id'];

        // Change the name of the newly created room through a PUT request
        static::createClient()->request('PUT', $roomId, [
            'json' => [
                'name' => 'Updated Room Name',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);
        self::assertResponseStatusCodeSame(405);
    }

    public function testPatchRoom(): void
    {
        $response = static::createClient()->request('POST', '/rooms', [
            'json' => [
                'hotelId' => $this->hotelId,
                'name' => 'Room 237',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);
        // Get id of newly created room from response
        $data = json_decode($response->getContent(), true);
        $roomId = $data['@id'];

        // Change the name of the newly created room through a PUT request
        static::createClient()->request('PATCH', $roomId, [
            'json' => [
                'name' => 'Updated Room Name',
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ],
        ]);
        self::assertResponseIsSuccessful();
        // Check response contains new name
        self::assertJsonContains([
            '@context' => '/contexts/Room',
            '@type' => 'Room',
            '@id' => $roomId,
            'name' => 'Updated Room Name',
        ]);

        static::createClient()->request('GET', $roomId);
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/contexts/Room',
            '@type' => 'Room',
            '@id' => $roomId,
            'name' => 'Updated Room Name',
        ]);
    }

    public function testDeleteRoom(): void
    {
        $response = static::createClient()->request('POST', '/rooms', [
            'json' => [
                'hotelId' => $this->hotelId,
                'name' => 'Room to be deleted',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        // Get id of newly created room from response
        $data = json_decode($response->getContent(), true);
        $roomId = $data['@id'];

        // Check that there is now 1 room
        static::createClient()->request('GET', '/rooms');
        self::assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/contexts/Room',
            '@id' => '/rooms',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 1,
        ]);

        // Make a request to delete the room
        static::createClient()->request('DELETE', $roomId);
        self::assertResponseStatusCodeSame(204);

        // Check that there are now 0 rooms
        static::createClient()->request('GET', '/rooms');
        self::assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/contexts/Room',
            '@id' => '/rooms',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 0,
        ]);
    }


//    public function testCreateAndReadBooking(): void
//    {
//        $client = static::createClient();
//        $client->disableReboot();
//        $response = $client->request('POST', '/rooms', [
//            'json' => [
//                'hotelId' => $this->hotelId,
//                'name' => 'Room 237',
//            ],
//            'headers' => [
//                'Content-Type' => 'application/ld+json',
//            ],
//        ]);
//        $data = json_decode($response->getContent(), true);
////        $roomId = $data['@id'];
//        $roomId = $data['id'];
//
//        $start = new DatePoint('today');
//        $end = new DatePoint('tomorrow');
//        $response = $client->request('POST', '/bookings', [
//            'json' => [
//                'roomId' => $roomId,
//                'startDate' => $start->format('Y-m-d'),
//                'endDate' => $end->format('Y-m-d')
//            ],
//            'headers' => [
//                'Content-Type' => 'application/ld+json',
//            ],
//        ]);
//        self::assertResponseIsSuccessful();
//
//        $response = $client->request('GET', '/rooms/' . $roomId);
//        self::assertResponseIsSuccessful();
//        // Get bookings array from response and count elements
//        $data = json_decode($response->getContent(), true);
//        $bookings = $data['bookings'];
//        var_dump($response->getContent());
//        $this->assertCount(1, $bookings);
//    }
}
