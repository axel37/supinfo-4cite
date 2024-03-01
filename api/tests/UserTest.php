<?php

namespace App\Tests;

use App\Exception\EmptyEmailException;
use App\Exception\EmptyNameException;
use PHPUnit\Framework\TestCase;
use App\Entity\User;

class UserTest extends TestCase
{
    public function testCanReadEmailAndUsername(): void
    {
        $user = new User('martin@example.com', 'martinfowler');

        $this->assertEquals('martin@example.com', $user->getEmail());
        $this->assertEquals('martinfowler', $user->getUsername());
    }

    public function testFailsOnEmptyEmail(): void
    {
        $this->expectException(EmptyEmailException::class);
        $user = new User(' ', 'martinfowler');
    }

    public function testFailsOnEmptyName(): void
    {
        $this->expectException(EmptyNameException::class);
        $user = new User('martin@example.com', ' ');
    }
}
