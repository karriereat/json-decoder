<?php

namespace Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Binding;
use Karriere\JsonDecoder\JsonDecoder;

class FieldBinding implements Binding
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
     * @var string
     */
    private $type;

    /**
     * @var bool
     */
    private $isRequired;

    /**
     * FieldBinding constructor.
     *
     * @param string $property   the property to bind to
     * @param string $jsonField  the json field
     * @param string $type       the desired type of the property
     * @param bool   $isRequired defines if the field value is required during decoding
     */
    public function __construct($property, $jsonField, $type, $isRequired = false)
    {
        $this->property = $property;
        $this->jsonField = $jsonField;
        $this->type = $type;
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
            $data = $jsonData[$this->jsonField];
            $propertyAccessor->set($jsonDecoder->decodeArray($data, $this->type));
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
