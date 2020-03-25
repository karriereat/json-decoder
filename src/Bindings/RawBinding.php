<?php

namespace Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Binding;

class RawBinding extends Binding
{
    /**
     * RawBinding constructor.
     *
     * @param string $property
     */
    public function __construct($property)
    {
        parent::__construct($property, null, null, false);
    }

    /**
     * {@inheritdoc}
     */
    public function validate($jsonData): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function bind($jsonDecoder, $jsonData, $propertyAccessor)
    {
        if (array_key_exists($this->property, $jsonData)) {
            $propertyAccessor->set($jsonData[$this->property]);
        }
    }
}
