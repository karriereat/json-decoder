<?php

namespace Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Binding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Property;

class AliasBinding extends Binding
{
    /**
     * AliasBinding constructor.
     *
     * @param string $property   the property to bind to
     * @param string $jsonField  the json field
     * @param bool   $isRequired defines if the field value is required during decoding
     */
    public function __construct(string $property, string $jsonField, bool $isRequired = false)
    {
        parent::__construct($property, $jsonField, null, $isRequired);
    }

    /**
     * {@inheritdoc}
     */
    public function bind(JsonDecoder $jsonDecoder, ?array $jsonData, Property $property)
    {
        if (array_key_exists($this->jsonField, $jsonData)) {
            $property->set($jsonData[$this->jsonField]);
        }
    }
}
