<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class RoomTest extends ApiTestCase
{
    public function testCanCreateRoom(): void
    {
        static::createClient()->request('POST', '/rooms', [
            'json' => [
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

}
