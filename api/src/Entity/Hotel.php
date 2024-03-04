<?php

namespace App\Entity;

use App\Exception\EmptyLocationException;
use App\Exception\EmptyNameException;
use App\Exception\RoomAlreadyInHotelException;
use App\Hotel\BookableInterface;
use App\Hotel\BookingInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
class Hotel
{
    #[Id]
    #[Column(type: UuidType::NAME)]
    private Uuid $id;
    /** @var Picture[] $pictures */
    private array $pictures = [];
    #[Column]
    private string $name;
    #[Column]
    private string $location;
    #[OneToMany(mappedBy: 'hotel', targetEntity: Room::class, cascade: ['persist', 'remove'])]
    private Collection $rooms;
    #[Column(nullable: true)]
    private ?string $description;

    /**
     * @param Room[] $rooms
     */
    public function __construct(
        string $name,
        string $location,
        /** @var iterable<BookableInterface> $rooms */
        array|Collection $rooms = [],
        ?string $description = null
    ) {
        $this->id = Uuid::v4();
        $this->setName($name);
        $this->setLocation($location);
        $this->rooms = new ArrayCollection($rooms);
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getRooms(): Collection
    {
        return $this->rooms;
    }

    public function getBookings(): iterable
    {
        /** @var iterable<BookingInterface> $bookings */
        $bookings = [];
        foreach ($this->rooms as $room) {
            foreach ($room->getBookings() as $booking) {
                $bookings[] = $booking;
            }
        }
        return $bookings;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPictures(): array
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): void
    {
        $this->pictures[] = $picture;
    }

    public function removePicture(Picture $picture): void
    {
        $this->pictures = array_udiff($this->pictures, [$picture], static fn(Picture $a, Picture $b) => $a <=> $b);
    }

    public function setName(string $name): void
    {
        if (trim($name) === '') {
            throw new EmptyNameException();
        }
        $this->name = $name;
    }

    public function setLocation(string $location): void
    {
        if (trim($location) === '') {
            throw new EmptyLocationException();
        }
        $this->location = $location;
    }

    public function removeRoom(Room $room): void
    {
        $this->rooms = new ArrayCollection(
            array_udiff($this->rooms->toArray(), [$room], static fn(Room $a, Room $b) => $a <=> $b)
        );
    }

    public function addRoom(Room $room): void
    {
        if ($this->rooms->contains($room)) {
            throw new RoomAlreadyInHotelException();
        }
        $this->rooms[] = $room;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function createRoom(string $name): Room
    {
        $newRoom = new Room($this, $name);
        $this->addRoom($newRoom);
        return $newRoom;
    }
}
