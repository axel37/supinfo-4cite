<?php

namespace App\Api;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Room;
use App\Exception\DtoIdAlreadySetException;
use App\State\Hotel\HotelStateProcessor;
use App\State\Hotel\HotelStateProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
//    normalizationContext: ['groups' => ['read']],
//    denormalizationContext: ['groups' => ['create', 'update']],
    provider: HotelStateProvider::class,
    processor: HotelStateProcessor::class,
)]
class HotelDto
{
    private Uuid $id;
    #[NotBlank]
    private string $name;
    #[NotBlank]
    private string $location;
    /** @var Collection<Room> */
    private Collection $rooms;
    #[NotBlank(allowNull: true)]
    private ?string $description;

    /**
     * @param string $name
     * @param string $location
     * @param iterable $rooms
     * @param string $description
     */
    public function __construct(string $name, string $location, ?Collection $rooms = null, ?string $description = null)
    {
        $this->name = $name;
        $this->location = $location;
        $this->rooms = $rooms ?? new ArrayCollection();
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

    public function getRooms(): Collection
    {
        return $this->rooms;
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
