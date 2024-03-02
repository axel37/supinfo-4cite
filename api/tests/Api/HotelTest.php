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
}
