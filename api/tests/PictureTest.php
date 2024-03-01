<?php

namespace App\Tests;

use App\Entity\Picture;
use App\Exception\EmptyPathException;
use PHPUnit\Framework\TestCase;

class PictureTest extends TestCase
{
    public function testCanReadPathAfterCreation(): void
    {
        $picture = new Picture('/fake/path/to/picture.jpg');
        $this->assertEquals('/fake/path/to/picture.jpg', $picture->getPath());
    }

    public function testFailOnEmptyLocation(): void
    {
        $this->expectException(EmptyPathException::class);
        $picture = new Picture(' ');
    }
}
