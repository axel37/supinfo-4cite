<?php

namespace App\Exception;

class BookingStartsAndEndsOnSameDayException extends \Exception
{
    public function __construct(string $message = "Booking can't end on the day it starts.", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
