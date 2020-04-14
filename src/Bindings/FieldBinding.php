<?php

namespace Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Binding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Property;

class FieldBinding extends Binding
{
    /**
     * {@inheritdoc}
     */
    public function bind(JsonDecoder $jsonDecoder, ?array $jsonData, Property $property)
    {
        if (array_key_exists($this->jsonField, $jsonData)) {
            $data = $jsonData[$this->jsonField];
            $property->set($jsonDecoder->decodeArray($data, $this->type));
        }
    }
}
