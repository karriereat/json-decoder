<?php

namespace Karriere\JsonDecoder\Attributes;

use Attribute;
use Karriere\JsonDecoder\Bindings\ArrayBinding;

#[Attribute]
class JsonArray implements AttributeInterface
{
    public function __construct(
        public string $className,
        public ?string $attribute = null,
        public bool $isRequired = false
    ) {
    }

    public function getBinding(string $propertyName)
    {
        $attribute = $this->attribute ?? $propertyName;

        return new ArrayBinding($propertyName, $attribute, $this->className, $this->isRequired);
    }
}
