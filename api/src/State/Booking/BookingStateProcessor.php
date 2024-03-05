<?php

namespace App\State\Booking;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Api\Assembler\BookingAssembler;
use App\Api\BookingDto;
use App\Exception\BookingNotFoundException;
use App\Exception\ProcessorOperationNotImplementedException;
use App\Exception\UnsupportedDtoException;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;

class BookingStateProcessor implements ProcessorInterface
{
    public function __construct(
        private BookingAssembler $assembler,
        private EntityManagerInterface $em,
        private BookingRepository $bookingRepository
    ) {
    }

    public function process(
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): BookingDto|null {
        if (!$data instanceof BookingDto) {
            throw new UnsupportedDtoException();
        }

        return match(true) {
            $operation instanceof Post => $this->post($data),
            $operation instanceof Delete => $this->delete($data),
            default => throw new ProcessorOperationNotImplementedException()
        };

    }

    private function post(BookingDto $dto): BookingDto
    {
        $room = $this->assembler->createBookingFromDto($dto);
        $this->em->persist($room);
        $this->em->flush();
        return $this->assembler->createDtoFromBooking($room);
    }

    public function delete(BookingDto $dto): null
    {
        $booking = $this->bookingRepository->find($dto->getId());
        if (!isset($booking)) {
            throw new BookingNotFoundException();
        }
        $this->em->remove($booking);
        $this->em->flush();
        return null;
    }
}
