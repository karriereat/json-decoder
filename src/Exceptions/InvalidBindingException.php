<?php

namespace Karriere\JsonDecoder\Exceptions;

use Exception;

class InvalidBindingException extends Exception
{
    public function __construct()
    {
        parent::__construct('the given binding must implement the Binding interface');
    }
}
