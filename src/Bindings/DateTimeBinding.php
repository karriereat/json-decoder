<?php

namespace Karriere\JsonDecoder\Bindings;

use DateTime;
use Karriere\JsonDecoder\Binding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Property;

class DateTimeBinding extends Binding
{
    /**
     * @var string
     */
    private $format;

    /**
     * DateTimeBinding constructor.
     *
     * @param string $property       the property to bind to
     * @param string $jsonField      the json field
     * @param bool   $isRequired     defines if the field value is required during decoding
     * @param string $dateTimeFormat defines the date format used for parsing, defaults to DateTime::ATOM
     */
    public function __construct(
        string $property,
        string $jsonField,
        bool $isRequired = false,
        string $dateTimeFormat = DateTime::ATOM
    ) {
        parent::__construct($property, $jsonField, null, $isRequired);

        $this->format = $dateTimeFormat;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $jsonData): bool
    {
        if (array_key_exists($this->jsonField, $jsonData) && !empty($jsonData[$this->jsonField])) {
            return DateTime::createFromFormat($this->format, $jsonData[$this->jsonField]) !== false;
        }

        return !$this->isRequired;
    }

    /**
     * {@inheritdoc}
     */
    public function bind(JsonDecoder $jsonDecoder, ?array $jsonData, Property $property)
    {
        if (array_key_exists($this->jsonField, $jsonData)) {
            $dateTimeObject = DateTime::createFromFormat($this->format, $jsonData[$this->jsonField]);

            if ($dateTimeObject !== false) {
                $property->set($dateTimeObject);
            }
        }
    }
}
