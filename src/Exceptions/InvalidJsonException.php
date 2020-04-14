<?php

namespace Karriere\JsonDecoder\Exceptions;

use Exception;

class InvalidJsonException extends Exception
{
    public function __construct()
    {
        parent::__construct('The given JSON input is not valid');
    }
}
