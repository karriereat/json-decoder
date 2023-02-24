<?php

namespace Karriere\JsonDecoder;

abstract class Binding
{
    public function __construct(
        protected string $property,
        protected ?string $jsonField = null,
        protected ?string $type = null,
        protected bool $isRequired = false,
    ) {
    }

    /**
     * validates the given binding data.
     */
    public function validate(array $jsonData): bool
    {
        return ! $this->isRequired || ($this->jsonField && array_key_exists($this->jsonField, $jsonData));
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
     */
    abstract public function bind(JsonDecoder $jsonDecoder, Property $property, array $jsonData = []): void;
}
