<?php

namespace App\State\Room;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Api\Assembler\RoomAssembler;
use App\Api\RoomDto;
use App\Exception\UnsupportedDtoException;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;

class RoomPostProcessor implements ProcessorInterface
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

        return $this->post($data);
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
}
