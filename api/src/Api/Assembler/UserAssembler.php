<?php

namespace App\Api\Assembler;

use App\Api\UserDto;
use App\Entity\User;

class UserAssembler
{
    public function createUserFromDto(UserDto $dto): User
    {
        $user = new User($dto->getEmail(), $dto->getUserName());
        $user->setPassword($dto->getPassword());
        return $user;
    }

    public function createDtoFromUser(User $user): UserDto
    {
        $dto = new UserDto($user->getEmail(), $user->getUsername(), '');
        $dto->initializeId($user->getId());
        return $dto;
    }

    public function updateUserFromDto(User $user, UserDto $dto): void
    {
        $user->setEmail($dto->getEmail());
        $user->setUsername($dto->getUserName());
    }
}
