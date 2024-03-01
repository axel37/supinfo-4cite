<?php

namespace App\Exception;

use Throwable;

class EmptyPathException extends \Exception
{
    public function __construct(string $message = "Path can't be empty.", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
