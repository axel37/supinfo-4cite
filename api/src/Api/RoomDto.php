<?php

namespace App\Api;

use ApiPlatform\Metadata\ApiResource;
use App\Exception\DtoIdAlreadySetException;
use App\State\RoomStateProcessor;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(shortName: 'Room', processor: RoomStateProcessor::class)]
class RoomDto
{
    #[NotBlank]
    private string $name;
    private Uuid $id;
    private Uuid $hotelId;

    public function __construct(Uuid $hotelId, string $name)
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

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getHotelId(): Uuid
    {
        return $this->hotelId;
    }

    public function setInitialId(Uuid $id): void
    {
        if (isset($this->id)) {
            throw new DtoIdAlreadySetException();
        }
        $this->id = $id;
    }
}
