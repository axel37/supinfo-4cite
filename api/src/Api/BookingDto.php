<?php

namespace App\Api;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Exception\DtoIdAlreadySetException;
use App\State\Booking\BookingStateProcessor;
use App\State\Booking\BookingStateProvider;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'Booking',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Delete()
    ],
    provider: BookingStateProvider::class,
    processor: BookingStateProcessor::class
)]
class BookingDto
{
    #[ApiProperty(identifier: true)]
    private Uuid $id;

    #[Assert\Uuid]
    #[Assert\NotBlank]
    private string $roomId;

    #[Assert\NotBlank]
    private \DateTimeInterface $startDate;
    #[Assert\NotBlank]
    private \DateTimeInterface $endDate;

    private string $ownerId;

    public function __construct(string $roomId, \DateTimeInterface $startDate, \DateTimeInterface $endDate, string $ownerId)
    {
        $this->roomId = $roomId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->ownerId = $ownerId;
    }

    public function getRoomId(): string
    {
        return $this->roomId;
    }

    public function getStartDate(): \DateTimeInterface
    {
        return $this->startDate;
    }

    public function getEndDate(): \DateTimeInterface
    {
        return $this->endDate;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function initializeId(Uuid $id): void
    {
        if (isset($this->id)) {
            throw new DtoIdAlreadySetException();
        }
        $this->id = $id;
    }

    public function getOwnerId(): string
    {
        return $this->ownerId;
    }

    public function setOwnerId(string $ownerId): void
    {
        $this->ownerId = $ownerId;
    }


}
