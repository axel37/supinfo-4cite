<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class UserTest extends ApiTestCase
{
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

}
