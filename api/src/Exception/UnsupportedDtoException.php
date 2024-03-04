<?php

namespace App\Exception;

class UnsupportedDtoException extends \Exception
{
    public function __construct(string $message = "Wrong DTO type", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
