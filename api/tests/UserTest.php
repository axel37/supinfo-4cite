<?php

namespace App\Tests;

use App\Exception\EmptyEmailException;
use PHPUnit\Framework\TestCase;
use App\Entity\User;

class UserTest extends TestCase
{
    public function testCanReadEmail(): void
    {
        $user = new User('martin@example.com');

        $this->assertEquals('martin@example.com', $user->getEmail());
    }

    public function testFailsOnEmptyEmail(): void
    {
        $this->expectException(EmptyEmailException::class);
        $user = new User(' ');
    }
}
