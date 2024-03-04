<?php

namespace App\Exception;

class DtoIdAlreadySetException extends \Exception
{
    public function __construct(string $message = "Attempted to set DTO's id when it was already set.", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
