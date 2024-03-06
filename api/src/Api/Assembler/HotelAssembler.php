<?php

namespace App\Api\Assembler;

use App\Api\HotelDto;
use App\Entity\Hotel;

class HotelAssembler
{
    public function createHotelFromDto(HotelDto $hotelDto): Hotel
    {
        $hotel = new Hotel($hotelDto->getName(), $hotelDto->getLocation(), description: $hotelDto->getDescription());
        return $hotel;
    }

    public function createDtoFromHotel(Hotel $hotel): HotelDto
    {
        $dto = new HotelDto($hotel->getName(), $hotel->getLocation(), $hotel->getRooms(), $hotel->getDescription());
        $dto->initializeId($hotel->getId());
        return $dto;
    }

    public function updateHotelFromDto(Hotel $hotel, HotelDto $dto): void
    {
        $hotel->setName($dto->getName());
        $hotel->setLocation($dto->getLocation());
        $hotel->setDescription($dto->getDescription());
    }
}
