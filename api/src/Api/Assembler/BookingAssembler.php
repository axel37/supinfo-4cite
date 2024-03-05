<?php

namespace App\Api\Assembler;

use App\Api\BookingDto;
use App\Entity\Booking;
use App\Exception\RoomNotFoundException;
use App\Repository\RoomRepository;

class BookingAssembler
{
    public function __construct(private RoomRepository $roomRepository)
    {
    }

    public function createBookingFromDto(BookingDto $dto): Booking
    {
        $room = $this->roomRepository->find($dto->getRoomId());
        if (!isset($room)) {
            throw new RoomNotFoundException();
        };
        $booking = $room->book($dto->getStartDate(), $dto->getEndDate());
        return $booking;
    }

    public function createDtoFromBooking(Booking $booking): BookingDto
    {
        $roomId = $booking->getRoom()->getId();
        $dto = new BookingDto($roomId, $booking->getStart(), $booking->getEnd());
        $dto->initializeId($booking->getId());
        return $dto;
    }
}
