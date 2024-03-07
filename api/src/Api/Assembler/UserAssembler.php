<?php

namespace App\Api\Assembler;

use App\Api\UserDto;
use App\Entity\User;
use App\Exception\BookingNotFoundException;
use App\Repository\BookingRepository;

class UserAssembler
{
    public function __construct(private BookingRepository $bookingRepository)
    {
    }

    public function createUserFromDto(UserDto $dto): User
    {
        $user = new User($dto->getEmail(), $dto->getUserName());
        foreach ($dto->getBookingIds() as $bookingId) {
            $booking = $this->bookingRepository->find($bookingId);
            if (!isset($booking)) {
                throw new BookingNotFoundException();
            }
            $user->addBooking($booking);
        }
        $user->setPassword($dto->getPassword());
        return $user;
    }

    public function createDtoFromUser(User $user): UserDto
    {
        $dto = new UserDto($user->getEmail(), $user->getUsername(), '', $user->getBookings());
        $dto->initializeId($user->getId());
        return $dto;
    }

    public function updateUserFromDto(User $user, UserDto $dto): void
    {
        $user->setEmail($dto->getEmail());
        $user->setUsername($dto->getUserName());
    }
}
