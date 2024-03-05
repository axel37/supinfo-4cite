<?php

namespace App\Exception;


use Throwable;

class ProcessorOperationNotImplementedException extends \Exception
{
    public function __construct(string $message = "This processor operation has not yet been implemented.", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
