<?php

namespace App\Exception;

use Throwable;

class RoomUnavailableForBookingException extends \Exception
{
    public function __construct(string $message = "This room already has a booking at these dates.", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
