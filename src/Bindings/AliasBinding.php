<?php

namespace Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Binding;

class AliasBinding extends Binding
{
    /**
     * AliasBinding constructor.
     *
     * @param string $property   the property to bind to
     * @param string $jsonField  the json field
     * @param bool   $isRequired defines if the field value is required during decoding
     */
    public function __construct($property, $jsonField, $isRequired = false)
    {
        parent::__construct($property, $jsonField, null, $isRequired);
    }

    /**
     * {@inheritdoc}
     */
    public function bind($jsonDecoder, $jsonData, $propertyAccessor)
    {
        if (array_key_exists($this->jsonField, $jsonData)) {
            $propertyAccessor->set($jsonData[$this->jsonField]);
        }
    }
}
