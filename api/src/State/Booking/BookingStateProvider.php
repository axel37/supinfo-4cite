<?php

namespace App\State\Booking;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\State\ProviderInterface;
use App\Api\Assembler\BookingAssembler;
use App\Api\BookingDto;
use App\Entity\Booking;
use App\Exception\ProviderOperationNotImplementedException;

class BookingStateProvider implements ProviderInterface
{
    public function __construct(
        private ItemProvider $itemProvider,
        private CollectionProvider $collectionProvider,
        private BookingAssembler $assembler
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return match (true) {
            $operation instanceof Get,
                $operation instanceof Delete => $this->getOne($operation, $uriVariables, $context),
            $operation instanceof GetCollection => $this->getCollection($operation, $uriVariables, $context),
            default => throw new ProviderOperationNotImplementedException()
        };
    }

    public function getOne(Operation $operation, array $uriVariables, array $context): BookingDto
    {
        $booking = $this->itemProvider->provide(
            $operation->withStateOptions(new Options(Booking::class)),
            $uriVariables,
            $context
        );

        return $this->assembler->createDtoFromBooking($booking);
    }

    public function getCollection(GetCollection $operation, array $uriVariables, array $context): TraversablePaginator
    {
        $bookings = $this->collectionProvider->provide(
            $operation->withStateOptions(new Options(Booking::class)),
            $uriVariables,
            $context
        );

        $dtos = [];
        foreach ($bookings as $booking) {
            $dtos[] = $this->assembler->createDtoFromBooking($booking);
        }

        return new TraversablePaginator(
            new \ArrayIterator($dtos),
            $bookings->getCurrentPage(),
            $bookings->getItemsPerPage(),
            $bookings->getTotalItems(),
        );
    }
}
