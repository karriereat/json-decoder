<?php

namespace Karriere\JsonDecoder\Attributes;

use Attribute;
use Karriere\JsonDecoder\Bindings\DateTimeBinding;

#[Attribute]
class JsonDateTime implements AttributeInterface
{
    public function __construct(
        public ?string $attribute = null,
        public bool $isRequired = false,
        public string $format = \DateTime::ATOM
    ) {
    }

    public function getBinding(string $propertyName)
    {
        $attribute = $this->attribute ?? $propertyName;

        return new DateTimeBinding($propertyName, $attribute, $this->isRequired, $this->format);
    }
}
