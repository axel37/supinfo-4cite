<?php

namespace App\Exception;

class ProviderOperationNotImplementedException extends \Exception
{
    public function __construct(string $message = "This provider operation has not yet been implemented.", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
