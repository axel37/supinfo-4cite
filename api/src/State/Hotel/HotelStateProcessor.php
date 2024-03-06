<?php

namespace App\State\Hotel;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Api\Assembler\HotelAssembler;
use App\Api\HotelDto;
use App\Exception\HotelNotFoundException;
use App\Exception\ProcessorOperationNotImplementedException;
use App\Exception\RoomNotFoundException;
use App\Exception\UnsupportedDtoException;
use App\Repository\HotelRepository;
use Doctrine\ORM\EntityManagerInterface;

class HotelStateProcessor implements ProcessorInterface
{
    public function __construct(
        private HotelAssembler $assembler,
        private EntityManagerInterface $em,
        private HotelRepository $hotelRepository
    ) {
    }

    public function process(
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): HotelDto|null {
        if (!$data instanceof HotelDto) {
            throw new UnsupportedDtoException();
        }

        return match(true) {
            $operation instanceof Post => $this->post($data),
            $operation instanceof Patch => $this->patch($data),
            $operation instanceof Delete => $this->delete($data),
            default => throw new ProcessorOperationNotImplementedException()
        };

    }

    private function post(HotelDto $dto): HotelDto
    {
        $hotel = $this->assembler->createHotelFromDto($dto);
        $this->em->persist($hotel);
        $this->em->flush();
        return $this->assembler->createDtoFromHotel($hotel);
    }


    public function patch(HotelDto $dto): HotelDto
    {
        $hotel = $this->hotelRepository->find($dto->getId());
        if (!isset($hotel)) {
            throw new HotelNotFoundException();
        }
        $this->assembler->updateHotelFromDto($hotel, $dto);
        $this->em->flush();
        return $this->assembler->createDtoFromHotel($hotel);
    }

    public function delete(HotelDto $dto): null
    {
        $hotel = $this->hotelRepository->find($dto->getId());
        if (!isset($hotel)) {
            throw new RoomNotFoundException();
        }
        $this->em->remove($hotel);
        $this->em->flush();
        return null;
    }
}
