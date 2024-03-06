<?php

namespace App\State\User;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Api\Assembler\UserAssembler;
use App\Api\UserDto;
use App\Exception\ProcessorOperationNotImplementedException;
use App\Exception\UnsupportedDtoException;
use App\Exception\UserNotFoundException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserStateProcessor implements ProcessorInterface
{
    public function __construct(
        private UserAssembler $assembler,
        private EntityManagerInterface $em,
        private UserRepository $UserRepository
    ) {
    }

    public function process(
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): UserDto|null {
        if (!$data instanceof UserDto) {
            throw new UnsupportedDtoException();
        }

        return match(true) {
            $operation instanceof Post => $this->post($data),
            $operation instanceof Patch => $this->patch($data),
            $operation instanceof Delete => $this->delete($data),
            default => throw new ProcessorOperationNotImplementedException()
        };

    }

    private function post(UserDto $dto): UserDto
    {
        $User = $this->assembler->createUserFromDto($dto);
        $this->em->persist($User);
        $this->em->flush();
        return $this->assembler->createDtoFromUser($User);
    }


    public function patch(UserDto $dto): UserDto
    {
        $User = $this->UserRepository->find($dto->getId());
        if (!isset($User)) {
            throw new UserNotFoundException();
        }
        $this->assembler->updateUserFromDto($User, $dto);
        $this->em->flush();
        return $this->assembler->createDtoFromUser($User);
    }

    public function delete(UserDto $dto): null
    {
        $User = $this->UserRepository->find($dto->getId());
        if (!isset($User)) {
            throw new UserNotFoundException();
        }
        $this->em->remove($User);
        $this->em->flush();
        return null;
    }
}
