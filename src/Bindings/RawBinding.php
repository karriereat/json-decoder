<?php

namespace Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Binding;

class RawBinding implements Binding
{
    /**
     * @var string
     */
    private $property;

    /**
     * RawBinding constructor.
     *
     * @param string $property
     */
    public function __construct($property)
    {
        $this->property = $property;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($jsonData) : bool
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

    /**
     * {@inheritdoc}
     */
    public function property()
    {
        return $this->property;
    }
}
