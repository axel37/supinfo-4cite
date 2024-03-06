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
use App\State\User\UserPasswordHasher;
use App\State\User\UserStateProcessor;
use App\State\User\UserStateProvider;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    shortName: 'User',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(processor: UserPasswordHasher::class),
        new Patch(processor: UserPasswordHasher::class),
        new Delete()
    ],
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['create', 'update']],
    provider: UserStateProvider::class,
    processor: UserStateProcessor::class,
)]
class UserDto implements PasswordAuthenticatedUserInterface
{
    #[Groups(['read'])]
    #[ApiProperty(identifier: true)]
    private Uuid $id;

    #[Email]
    #[NotBlank]
    #[Groups(['read', 'create', 'update'])]
    private string $email;
    #[NotBlank]
    #[Groups(['read', 'create', 'update'])]
    private string $userName;
    #[NotBlank(groups: ['create'])]
    #[Groups(['create', 'update'])]
    private string $plainPassword;

    public function __construct(string $email, string $userName, string $plainPassword)
    {
        $this->email = $email;
        $this->userName = $userName;
        $this->plainPassword = $plainPassword;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function initializeId(Uuid $id): void
    {
        if (isset($this->id)) {
            throw new DtoIdAlreadySetException();
        }
        $this->id = $id;
    }

    public function setPassword(string $password): void
    {
        $this->plainPassword = $password;
    }


    public function getPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function eraseCredentials(): void
    {
        return;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }
}
