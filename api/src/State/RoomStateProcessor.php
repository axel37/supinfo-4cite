<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Api\Assembler\RoomAssembler;
use App\Api\RoomDto;
use App\Exception\OperationNotImplementedException;
use App\Exception\RoomNotFoundException;
use App\Exception\UnsupportedDtoException;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;

class RoomStateProcessor implements ProcessorInterface
{
    public function __construct(
        private RoomAssembler $assembler,
        private EntityManagerInterface $em,
        private RoomRepository $roomRepository
    ) {
    }

    public function process(
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): RoomDto|null {
        if (!$data instanceof RoomDto) {
            throw new UnsupportedDtoException();
        }

        return match (true) {
            $operation instanceof Post => $this->post($data),
            default => throw new OperationNotImplementedException()
        };
    }

    /*
     * Turn the DTO into an entity, persist it, and return a matching DTO.
     */
    private function post(RoomDto $dto): RoomDto
    {
        $room = $this->assembler->createRoomFromDto($dto);
        $this->em->persist($room);
        $this->em->flush();
        return $this->assembler->createDtoFromRoom($room);
    }

    public function patch(RoomDto $dto): RoomDto
    {
        $room = $this->roomRepository->find($dto->getId());
        if (!isset($room)) {
            throw new RoomNotFoundException();
        }
        $this->assembler->updateRoomFromDto($room, $dto);
        return $this->assembler->createDtoFromRoom($room);
    }
}
