<?php

namespace App\Api\Assembler;

use App\Api\RoomDto;
use App\Entity\Room;
use App\Exception\HotelNotFoundException;
use App\Repository\HotelRepository;

class RoomAssembler
{
    public function __construct(private HotelRepository $hotelRepository)
    {
    }

    public function createRoomFromDto(RoomDto $roomDto): Room
    {
        $hotel = $this->hotelRepository->find($roomDto->getHotelId());
        if (!isset($hotel)) {
            throw new HotelNotFoundException();
        }
        $room = new Room($hotel, $roomDto->getName());
        return $room;
    }

    public function createDtoFromRoom(Room $room): RoomDto
    {
        $hotelId = $room->getHotel()->getId();
        $dto = new RoomDto($hotelId, $room->getName());
        $dto->setInitialId($room->getId());
        return $dto;
    }
}
