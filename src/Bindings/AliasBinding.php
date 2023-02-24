<?php

namespace Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Binding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Property;

class AliasBinding extends Binding
{
    public function __construct(string $property, string $jsonField, bool $isRequired = false)
    {
        parent::__construct(property: $property, jsonField: $jsonField, isRequired: $isRequired);
    }

    public function bind(JsonDecoder $jsonDecoder, Property $property, array $jsonData = []): void
    {
        if ($this->jsonField && array_key_exists($this->jsonField, $jsonData)) {
            $property->set($jsonData[$this->jsonField]);
        }
    }
}
