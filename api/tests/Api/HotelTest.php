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
            'rooms' => [],
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

        // Check that there is now 1 room
        $response = static::createClient()->request('GET', '/hotels');
        self::assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/contexts/Hotel',
            '@id' => '/hotels',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 1,
        ]);
    }

}
