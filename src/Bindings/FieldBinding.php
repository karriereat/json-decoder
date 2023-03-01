<?php

namespace Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Binding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Property;

class FieldBinding extends Binding
{
    public function bind(JsonDecoder $jsonDecoder, Property $property, array $jsonData = []): void
    {
        if ($this->jsonField && array_key_exists($this->jsonField, $jsonData) && $this->type) {
            $data = $jsonData[$this->jsonField];
            $property->set($jsonDecoder->decodeArray($data, $this->type));
        }
    }
}
