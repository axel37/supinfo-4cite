<?php

namespace App\Api;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Serializer\Attribute\Groups;
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
class UserDto
{
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
}
