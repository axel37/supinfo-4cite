<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class HotelTest extends ApiTestCase
{
    public function testCanCreateHotel(): void
    {
        // TODO : "/hotel" may not be correct... could it be /hotels ? We'll see ;)
        static::createClient()->request('POST', '/hotel', [
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
            '@context' => '/contexts/hotel',
            '@type' => 'Hotel',
            'name' => 'Grand Hotel',
            'location' => 'Pine Street',
        ]);
    }
}
