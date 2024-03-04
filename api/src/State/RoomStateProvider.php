<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\State\ProviderInterface;
use App\Api\Assembler\RoomAssembler;
use App\Api\RoomDto;
use App\Entity\Room;
use App\Exception\OperationNotImplementedException;

class RoomStateProvider implements ProviderInterface
{
    public function __construct(
        private ItemProvider $itemProvider,
        private CollectionProvider $collectionProvider,
        private RoomAssembler $assembler
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return match (true) {
            $operation instanceof Get => $this->getOne($operation, $uriVariables, $context),
            $operation instanceof GetCollection => $this->getCollection($operation, $uriVariables, $context),
            default => throw new OperationNotImplementedException()
        };
    }

    public function getOne(Get $operation, array $uriVariables, array $context): RoomDto
    {
        $room = $this->itemProvider->provide(
            $operation->withStateOptions(new Options(Room::class)),
            $uriVariables,
            $context
        );

        return $this->assembler->createDtoFromRoom($room);
    }

    public function getCollection(GetCollection $operation, array $uriVariables, array $context): TraversablePaginator
    {
        $rooms = $this->collectionProvider->provide(
            $operation->withStateOptions(new Options(Room::class)),
            $uriVariables,
            $context
        );

        $dtos = [];
        foreach ($rooms as $room) {
            $dtos[] = $this->assembler->createDtoFromRoom($room);
        }

        return new TraversablePaginator(
            new \ArrayIterator($dtos),
            $rooms->getCurrentPage(),
            $rooms->getItemsPerPage(),
            $rooms->getTotalItems(),
        );
    }
}
