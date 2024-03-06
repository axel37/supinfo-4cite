<?php

namespace App\State\Hotel;

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
use App\Api\Assembler\HotelAssembler;
use App\Api\HotelDto;
use App\Entity\Hotel;
use App\Exception\ProviderOperationNotImplementedException;

class HotelStateProvider implements ProviderInterface
{
    public function __construct(
        private ItemProvider $itemProvider,
        private CollectionProvider $collectionProvider,
        private HotelAssembler $assembler
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

    public function getOne(Operation $operation, array $uriVariables, array $context): HotelDto
    {
        $room = $this->itemProvider->provide(
            $operation->withStateOptions(new Options(Hotel::class)),
            $uriVariables,
            $context
        );

        return $this->assembler->createDtoFromHotel($room);
    }

    public function getCollection(GetCollection $operation, array $uriVariables, array $context): TraversablePaginator
    {
        $hotels = $this->collectionProvider->provide(
            $operation->withStateOptions(new Options(Hotel::class)),
            $uriVariables,
            $context
        );

        $dtos = [];
        foreach ($hotels as $hotel) {
            $dtos[] = $this->assembler->createDtoFromHotel($hotel);
        }

        return new TraversablePaginator(
            new \ArrayIterator($dtos),
            $hotels->getCurrentPage(),
            $hotels->getItemsPerPage(),
            $hotels->getTotalItems(),
        );
    }
}
