<?php

namespace Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Binding;
use Karriere\JsonDecoder\Exceptions\JsonValueException;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\PropertyAccessor;

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
     * AliasBinding constructor.
     *
     * @param string $property  the property to bind to
     * @param string $jsonField the json field
     */
    public function __construct($property, $jsonField)
    {
        $this->property = $property;
        $this->jsonField = $jsonField;
    }

    /**
     * executes the defined binding method on the class instance.
     *
     * @param JsonDecoder $jsonDecoder
     * @param mixed       $jsonData
     * @param PropertyAccessor $propertyAccessor    the class instance to bind to
     *
     * @throws JsonValueException if given json field is not available
     *
     * @return mixed
     */
    public function bind($jsonDecoder, $jsonData, $propertyAccessor)
    {
        if (!array_key_exists($this->jsonField, $jsonData)) {
            throw new JsonValueException(
                sprintf('the value "%s" for property "%s" does not exist', $this->jsonField, $this->property)
            );
        }

        $propertyAccessor->set($jsonData[$this->jsonField]);
    }

    /**
     * @return string the name of the property to bind
     */
    public function property()
    {
        return $this->property;
    }
}
