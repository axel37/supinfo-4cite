<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Api\Assembler\RoomAssembler;
use App\Api\RoomDto;
use App\Exception\UnsupportedDtoException;
use Doctrine\ORM\EntityManagerInterface;

class RoomStateProcessor implements ProcessorInterface
{
    public function __construct(private RoomAssembler $assembler, EntityManagerInterface $entityManager)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): RoomDto|null
    {
        if (!$data instanceof RoomDto) {
            throw new UnsupportedDtoException();
        }
        if ($operation instanceof Post) {
            /** @var RoomDto $dto */
            $dto = $data;
            // TODO get RoomDto, make entity from it, persist entity
            // TODO : How do we handle updates ??
            // return Room ? or roomdto ?

            $room = $this->assembler->createRoomFromDto($dto);
            return $this->assembler->createDtoFromRoom($room);
        }
    }
}
