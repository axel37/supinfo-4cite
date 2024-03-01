<?php

namespace App\Tests;

use App\Entity\Room;
use App\Exception\EmptyEmailException;
use App\Exception\EmptyNameException;
use PHPUnit\Framework\TestCase;
use App\Entity\User;
use Symfony\Component\Clock\DatePoint;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = new User('martin@example.com', 'martinfowler');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->user);
    }

    public function testCanReadEmailAndUsername(): void
    {
        $this->assertEquals('martin@example.com', $this->user->getEmail());
        $this->assertEquals('martinfowler', $this->user->getUsername());
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

    public function testCanUpdateEmailAndUsername(): void
    {
        $this->user->setEmail('martinfowler@example.com');
        $this->user->setUsername('martin');

        $this->assertEquals('martinfowler@example.com', $this->user->getEmail());
        $this->assertEquals('martin', $this->user->getUsername());
    }

    public function testFailsOnUpdateInvalidEmail(): void
    {
        $this->expectException(EmptyEmailException::class);
        $this->user->setEmail(' ');
    }

    public function testFailOnUpdateInvalidUsername(): void
    {
        $this->expectException(EmptyNameException::class);
        $this->user->setUsername(' ');
    }

    public function testCanBookRoom(): void
    {
        $room = new Room('Room 237');

        $this->user->book($room, new DatePoint('today'), new DatePoint('tomorrow'));
        $this->assertCount(1, $this->user->getBookings());
        $this->assertCount(1, $room->getBookings());
    }
}
