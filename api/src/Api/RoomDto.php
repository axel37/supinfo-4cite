<?php

namespace App\Api;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Booking;
use App\Exception\DtoIdAlreadySetException;
use App\State\Room\RoomStateProcessor;
use App\State\Room\RoomStateProvider;
use Symfony\Component\Serializer\Attribute\Groups;
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
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['create', 'update']],
    provider: RoomStateProvider::class,
    processor: RoomStateProcessor::class,
)]
class RoomDto
{
    #[Assert\NotBlank]
    #[Groups(['read', 'create', 'update'])]
    private string $name;

    #[ApiProperty(identifier: true)]
    #[Groups(['read'])]
    private Uuid $id;

    #[Assert\Uuid]
    #[Assert\NotBlank]
    #[Groups(['read', 'create'])]
    private string $hotelId;

    /** @var Booking[] $bookings */
    #[Groups(['read'])]
    private array $bookings;

    public function __construct(string $hotelId, string $name, array $bookings =  [])
    {
        $this->hotelId = $hotelId;
        $this->name = $name;
        $this->bookings = $bookings;
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

    public function getBookings(): array
    {
        return $this->bookings;
    }
}
