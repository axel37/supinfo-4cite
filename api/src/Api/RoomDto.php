<?php

namespace App\Api;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Exception\DtoIdAlreadySetException;
use App\State\RoomStateProcessor;
use App\State\RoomStateProvider;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'Room',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Patch(),
        new Delete()
    ],
    provider: RoomStateProvider::class,
    processor: RoomStateProcessor::class
)]
class RoomDto
{
    #[Assert\NotBlank]
    private string $name;

    #[ApiProperty(identifier: true)]
    private Uuid $id;

    #[Assert\Uuid]
    #[Assert\NotBlank]
    private string $hotelId;

    public function __construct(string $hotelId, string $name)
    {
        $this->hotelId = $hotelId;
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

    public function getId(): string
    {
        return $this->id;
    }

    public function getHotelId(): string
    {
        return $this->hotelId;
    }

    public function initializeId(Uuid $id): void
    {
        if (isset($this->id)) {
            throw new DtoIdAlreadySetException();
        }
        $this->id = $id;
    }
}
