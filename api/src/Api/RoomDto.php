<?php

namespace App\Api;

use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(shortName: 'Room')]
class RoomDto
{
    #[NotBlank]
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
