<?php

namespace Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Binding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Property;

class RawBinding extends Binding
{
    /**
     * RawBinding constructor.
     */
    public function __construct(string $property)
    {
        parent::__construct($property, null, null, false);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $jsonData): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function bind(JsonDecoder $jsonDecoder, ?array $jsonData, Property $property)
    {
        if (array_key_exists($this->property, $jsonData)) {
            $property->set($jsonData[$this->property]);
        }
    }
}
