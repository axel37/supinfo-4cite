<?php

namespace App\State\User;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\State\ProviderInterface;
use App\Api\Assembler\UserAssembler;
use App\Api\UserDto;
use App\Entity\User;
use App\Exception\ProviderOperationNotImplementedException;

class UserStateProvider implements ProviderInterface
{
    public function __construct(
        private ItemProvider $itemProvider,
        private CollectionProvider $collectionProvider,
        private UserAssembler $assembler
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return match (true) {
            $operation instanceof Get,
                $operation instanceof Patch,
                $operation instanceof Delete => $this->getOne($operation, $uriVariables, $context),
            $operation instanceof GetCollection => $this->getCollection($operation, $uriVariables, $context),
            default => throw new ProviderOperationNotImplementedException()
        };
    }

    public function getOne(Operation $operation, array $uriVariables, array $context): UserDto
    {
        $User = $this->itemProvider->provide(
            $operation->withStateOptions(new Options(User::class)),
            $uriVariables,
            $context
        );

        return $this->assembler->createDtoFromUser($User);
    }

    public function getCollection(GetCollection $operation, array $uriVariables, array $context): TraversablePaginator
    {
        $Users = $this->collectionProvider->provide(
            $operation->withStateOptions(new Options(User::class)),
            $uriVariables,
            $context
        );

        $dtos = [];
        foreach ($Users as $User) {
            $dtos[] = $this->assembler->createDtoFromUser($User);
        }

        return new TraversablePaginator(
            new \ArrayIterator($dtos),
            $Users->getCurrentPage(),
            $Users->getItemsPerPage(),
            $Users->getTotalItems(),
        );
    }
}
