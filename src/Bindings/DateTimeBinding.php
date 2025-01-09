<?php

namespace Karriere\JsonDecoder\Bindings;

use DateTime;
use DateTimeInterface;
use Karriere\JsonDecoder\Binding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Property;

class DateTimeBinding extends Binding
{
    public function __construct(
        string $property,
        ?string $jsonField = null,
        bool $isRequired = false,
        private string $dateTimeFormat = DateTimeInterface::ATOM
    ) {
        parent::__construct(property: $property, jsonField: $jsonField, isRequired: $isRequired);
    }

    public function validate(array $jsonData): bool
    {
        if ($this->jsonField && array_key_exists($this->jsonField, $jsonData) && ! empty($jsonData[$this->jsonField])) {
            if (! is_string($jsonData[$this->jsonField])) {
                return false;
            }
            return DateTime::createFromFormat($this->dateTimeFormat, $jsonData[$this->jsonField]) !== false;
        }

        return ! $this->isRequired;
    }

    public function bind(JsonDecoder $jsonDecoder, Property $property, array $jsonData = []): void
    {
        if ($this->jsonField && array_key_exists($this->jsonField, $jsonData)) {
            if (is_string($jsonData[$this->jsonField])) {
                $dateTimeObject = DateTime::createFromFormat($this->dateTimeFormat, $jsonData[$this->jsonField]);

                if ($dateTimeObject !== false) {
                    $property->set($dateTimeObject);
                }
            }
        }
    }
}
