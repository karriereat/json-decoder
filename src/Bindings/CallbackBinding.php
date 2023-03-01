<?php

namespace Karriere\JsonDecoder\Bindings;

use Closure;
use Karriere\JsonDecoder\Binding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Property;

class CallbackBinding extends Binding
{
    public function __construct(string $property, private Closure $callback)
    {
        parent::__construct($property);
    }

    public function validate(array $jsonData): bool
    {
        return true;
    }

    public function bind(JsonDecoder $jsonDecoder, Property $property, array $jsonData = []): void
    {
        $property->set($this->callback->__invoke($jsonData, $jsonDecoder));
    }
}
