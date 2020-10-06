<?php

namespace Karriere\JsonDecoder;

abstract class Binding
{
    /**
     * @var string
     */
    protected $property;

    /**
     * @var string
     */
    protected $jsonField;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $isRequired;

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
        $this->property   = $property;
        $this->jsonField  = $jsonField;
        $this->type       = $type;
        $this->isRequired = $isRequired;
    }

    /**
     * validates the given binding data.
     *
     * @param mixed $jsonData
     */
    public function validate(array $jsonData): bool
    {
        return !$this->isRequired || array_key_exists($this->jsonField, $jsonData);
    }

    /**
     * @return string the name of the property to bind
     */
    public function property(): string
    {
        return $this->property;
    }

    /**
     * @return string the name of the json field to bind
     */
    public function jsonField(): string
    {
        return $this->jsonField ?? $this->property;
    }

    /**
     * executes the defined binding method on the class instance.
     *
     * @param mixed    $jsonData
     * @param Property $property the class instance to bind to
     *
     * @return mixed
     */
    abstract public function bind(JsonDecoder $jsonDecoder, ?array $jsonData, Property $property);
}
