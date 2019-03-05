<?php

namespace Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Binding;

class AliasBinding implements Binding
{
    /**
     * @var string
     */
    private $property;

    /**
     * @var string
     */
    private $jsonField;

    /**
     * @var bool
     */
    private $isRequired;

    /**
     * AliasBinding constructor.
     *
     * @param string $property   the property to bind to
     * @param string $jsonField  the json field
     * @param bool   $isRequired defines if the field value is required during decoding
     */
    public function __construct($property, $jsonField, $isRequired = false)
    {
        $this->property = $property;
        $this->jsonField = $jsonField;
        $this->isRequired = $isRequired;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($jsonData) : bool
    {
        return !$this->isRequired || array_key_exists($this->jsonField, $jsonData);
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

    /**
     * {@inheritdoc}
     */
    public function property()
    {
        return $this->property;
    }
}
