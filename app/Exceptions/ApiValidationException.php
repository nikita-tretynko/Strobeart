<?php

namespace App\Exceptions;

use Exception;

class ApiValidationException extends Exception
{
    protected $message = 'Validation Error.';
}
