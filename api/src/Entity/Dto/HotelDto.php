<?php

namespace App\Entity\Dto;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Room;
use App\Hotel\BookableInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(shortName: 'Hotel')]
class HotelDto
{
    #[NotBlank]
    private string $name;
    #[NotBlank]
    private string $location;
    /** @var Room[] */
    private array $rooms;
    #[NotBlank(allowNull: true)]
    private ?string $description;

    /**
     * @param string $name
     * @param string $location
     * @param iterable $rooms
     * @param string $description
     */
    public function __construct(string $name, string $location, array $rooms = [], ?string $description = null)
    {
        $this->name = $name;
        $this->location = $location;
        $this->rooms = $rooms;
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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getRooms(): array
    {
        return $this->rooms;
    }


}
