<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class HotelTest extends ApiTestCase
{
    public function testCanCreateHotel(): void
    {
        static::createClient()->request('POST', '/hotels', [
            'json' => [
                'name' => 'Grand Hotel',
                'location' => 'Pine Street',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        self::assertResponseStatusCodeSame(201);
        self::assertJsonContains([
            '@context' => '/contexts/Hotel',
            '@type' => 'Hotel',
            'name' => 'Grand Hotel',
            'location' => 'Pine Street',
            'roomIds' => []
        ]);
    }

    public function testFailsOnInvalidName(): void
    {
        static::createClient()->request('POST', '/hotels', [
            'json' => [
                'name' => '',
                'location' => 'Pine Street',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);
        self::assertResponseStatusCodeSame(422);
    }

    public function testFailsOnInvalidLocation(): void
    {
        static::createClient()->request('POST', '/hotels', [
            'json' => [
                'name' => 'Grand Hotel',
                'location' => '',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);
        self::assertResponseStatusCodeSame(422);
    }

    public function testCreateThenFindHotel(): void
    {
        $response = static::createClient()->request('POST', '/hotels', [
            'json' => [
                'name' => 'Grand Hotel',
                'location' => 'Pine Street',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        // Get id of newly created room from response
        $data = json_decode($response->getContent(), true);
        $hotelUri = $data['@id'];

        // Check that there is now 1 hotel
        static::createClient()->request('GET', '/hotels');
        self::assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/contexts/Hotel',
            '@id' => '/hotels',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 1,
        ]);

        // Check that we can get the new hotel and it has all the info we expect
        static::createClient()->request('GET', $hotelUri);
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/contexts/Hotel',
            '@id' => $hotelUri,
            '@type' => 'Hotel',
            'name' => 'Grand Hotel',
            'location' => 'Pine Street',
            'roomIds' => []
        ]);
    }

    public function testCreateRoomThenReadHotel(): void
    {
        $response = static::createClient()->request('POST', '/hotels', [
            'json' => [
                'name' => 'Hotel which will have a room',
                'location' => 'No rooms street',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);
        self::assertResponseIsSuccessful();

        // Get id of newly created room from response
        $data = json_decode($response->getContent(), true);
        $hotelUri = $data['@id'];
        $hotelId = $data['id'];

        $response = static::createClient()->request('POST', '/rooms', [
            'json' => [
                'hotelId' => $hotelId,
                'name' => 'Newly added room',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);
        self::assertResponseIsSuccessful();
        $data = json_decode($response->getContent(), true);
        $roomId = $data['id'];


        // GET hotel and check that it has one room
        static::createClient()->request('GET', $hotelUri);
        self::assertJsonContains([
            '@context' => '/contexts/Hotel',
            '@id' => $hotelUri,
            '@type' => 'Hotel',
            'name' => 'Hotel which will have a room',
            'location' => 'No rooms street',
            'roomIds' => [
                0 => $roomId
            ],
        ]);
    }

}
