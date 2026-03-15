<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class CustomException extends HttpException
{
    public function __construct(
        int $statusCode = 403,
        string $message = 'Requested access to resource is denied',
        ?Throwable $previous = null
    ) {
        parent::__construct($statusCode, $message, $previous);
    }
}
