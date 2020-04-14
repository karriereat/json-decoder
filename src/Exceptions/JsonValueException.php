<?php

namespace Karriere\JsonDecoder\Exceptions;

use Exception;

class JsonValueException extends Exception
{
    public function __construct($propertyName)
    {
        parent::__construct(
            sprintf(
                'Unable to bind required property "%s" because JSON data is missing or invalid',
                $propertyName
            )
        );
    }
}
