<?php

namespace App\Exception;

class RoomAlreadyInHotelException extends \Exception
{
    public function __construct(string $message = "Room is already part of hotel.", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
