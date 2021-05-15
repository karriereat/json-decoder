<?php

namespace Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Binding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Property;

class HideUnmappedBinding extends Binding
{


    /**
     * {@inheritdoc}
     */
    public function bind(JsonDecoder $jsonDecoder, ?array $jsonData, Property $property)
    {
    }
}
