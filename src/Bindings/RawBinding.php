<?php

namespace Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Binding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Property;

class RawBinding extends Binding
{
    public function validate(array $jsonData): bool
    {
        return true;
    }

    public function bind(JsonDecoder $jsonDecoder, Property $property, array $jsonData = []): void
    {
        if (array_key_exists($this->property, $jsonData)) {
            $property->set($jsonData[$this->property]);
        }
    }
}
