<?php

namespace App\Exception;

use Throwable;

class BookingInThePastException extends \Exception
{
    public function __construct(string $message = "New bookings must be in the future.", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
