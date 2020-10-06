<?php

namespace Karriere\JsonDecoder\Exceptions;

use Exception;

class NotExistingRootException extends Exception
{
    public function __construct(string $root)
    {
        parent::__construct(sprintf('Root "%s" does not exist', $root));
    }
}
