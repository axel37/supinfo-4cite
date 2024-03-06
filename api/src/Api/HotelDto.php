<?php

namespace App\Api;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Exception\DtoIdAlreadySetException;
use App\State\Hotel\HotelStateProcessor;
use App\State\Hotel\HotelStateProvider;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    shortName: 'Hotel',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Patch(),
        new Delete()
    ],
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['create', 'update']],
    provider: HotelStateProvider::class,
    processor: HotelStateProcessor::class,
)]
class HotelDto
{
    #[Groups(['read'])]
    private Uuid $id;
    #[NotBlank]
    #[Groups(['read', 'create', 'update'])]
    private string $name;
    #[NotBlank]
    #[Groups(['read', 'create', 'update'])]
    private string $location;

    #[Groups(['read'])]
    /** @var string[] $roomIds */
    private array $roomIds;
    #[NotBlank(allowNull: true)]
    #[Groups(['read', 'create', 'update'])]
    private ?string $description;

    /**
     * @param string $name
     * @param string $location
     * @param iterable $rooms
     * @param string $description
     */
    public function __construct(string $name, string $location, array $roomIds = [], ?string $description = null)
    {
        $this->name = $name;
        $this->location = $location;
        $this->roomIds = $roomIds;
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): void
    {
        $this->location = $location;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getRoomIds(): array
    {
        return $this->roomIds;
    }

    public function initializeId(Uuid $id): void
    {
        if (isset($this->id)) {
            throw new DtoIdAlreadySetException();
        }
        $this->id = $id;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }


}
