<?php

namespace App\Exception;

use ApiPlatform\Metadata\Exception\ProblemExceptionInterface;
use Throwable;

class RoomUnavailableForBookingException extends \Exception implements ProblemExceptionInterface
{
    public function __construct(string $message = "This room already has a booking at these dates.", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getType(): string
    {
        return static::class;
    }

    public function getTitle(): ?string
    {
        return $this->message;
    }

    public function getStatus(): ?int
    {
        return 409;
    }

    public function getDetail(): ?string
    {
        return null;
    }

    public function getInstance(): ?string
    {
        return null;
    }
}
