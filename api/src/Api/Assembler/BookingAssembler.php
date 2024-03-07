<?php

namespace App\Api\Assembler;

use App\Api\BookingDto;
use App\Entity\Booking;
use App\Exception\RoomNotFoundException;
use App\Exception\UserNotFoundException;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;

class BookingAssembler
{
    public function __construct(private RoomRepository $roomRepository, private UserRepository $userRepository)
    {
    }

    public function createBookingFromDto(BookingDto $dto): Booking
    {
        $room = $this->roomRepository->find($dto->getRoomId());
        if (!isset($room)) {
            throw new RoomNotFoundException();
        };
        $owner = $this->userRepository->find($dto->getOwnerId());
        if (!isset($owner)) {
            throw new UserNotFoundException();
        }
        $booking = $room->book($dto->getStartDate(), $dto->getEndDate());
        $booking->setOwner($owner);
        return $booking;
    }

    public function createDtoFromBooking(Booking $booking): BookingDto
    {
        $roomId = $booking->getRoom()->getId();
        $ownerId = $booking->getOwner()->getId();
        $dto = new BookingDto($roomId, $booking->getStart(), $booking->getEnd(), $ownerId);
        $dto->initializeId($booking->getId());
        return $dto;
    }
}
