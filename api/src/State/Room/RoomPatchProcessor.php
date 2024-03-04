<?php

namespace App\State\Room;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Api\Assembler\RoomAssembler;
use App\Api\RoomDto;
use App\Exception\RoomNotFoundException;
use App\Exception\UnsupportedDtoException;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;

class RoomPatchProcessor implements ProcessorInterface
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

        return $this->patch($data);
    }


    public function patch(RoomDto $dto): RoomDto
    {
        $room = $this->roomRepository->find($dto->getId());
        if (!isset($room)) {
            throw new RoomNotFoundException();
        }
        $this->assembler->updateRoomFromDto($room, $dto);
        $this->em->flush();
        return $this->assembler->createDtoFromRoom($room);
    }
}
