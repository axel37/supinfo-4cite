<?php

namespace App\Exception;

class BookingEndsBeforeStartingException extends \Exception
{
    public function __construct(string $message = "Wrong booking dates : end is before start.", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
