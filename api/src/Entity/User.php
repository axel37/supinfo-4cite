<?php

namespace App\Entity;

use App\Exception\EmptyEmailException;
use App\Exception\EmptyNameException;
use App\Hotel\BookingInterface;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[Entity(repositoryClass: UserRepository::class)]
#[Table(name: 'app_user')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[Id]
    #[Column(type: UuidType::NAME)]
    private Uuid $id;
    #[Column(length: 320, unique: true)]
    private string $email;
    #[Column]
    private string $userName;
    #[Column]
    private string $password;
    #[Column(type: Types::JSON)]
    private array $roles;

    /** @var Collection<BookingInterface> */
    #[OneToMany(mappedBy: 'owner', targetEntity: Booking::class, cascade: ['persist', 'remove'])]
    private Collection $bookings;

    public function __construct(string $email, string $username)
    {
        $this->id = Uuid::v4();
        $this->setEmail($email);
        $this->setUsername($username);
        $this->roles = [];
        $this->bookings = new ArrayCollection();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->userName;
    }

    public function book(Room $room, \DateTimeInterface $start, \DateTimeInterface $end): void
    {
        $this->bookings[] = $room->book($start, $end);
    }

    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): void
    {
        $this->bookings[] = $booking;
    }

    public function setEmail(string $email): void
    {
        if (trim($email) === '') {
            throw new EmptyEmailException();
        }
        $this->email = $email;
    }

    public function setUsername(string $username): void
    {
        if (trim($username) === '') {
            throw new EmptyNameException();
        }
        $this->userName = $username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
        // Not needed
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }
}
