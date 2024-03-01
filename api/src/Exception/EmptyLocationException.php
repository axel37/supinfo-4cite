<?php

namespace App\Exception;

class EmptyLocationException extends \Exception
{
    public function __construct(string $message = "Location can't be empty.", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
