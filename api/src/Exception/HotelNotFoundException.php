<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HotelNotFoundException extends NotFoundHttpException
{
    public function __construct(string $message = 'Specified Hotel does not exist.', ?\Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct($message, $previous, $code, $headers);
    }
}
