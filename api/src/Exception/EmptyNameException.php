<?php

namespace App\Exception;

use Throwable;

class EmptyNameException extends \Exception
{
    public function __construct(string $message = "Name can't be empty", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
