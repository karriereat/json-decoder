<?php

namespace Karriere\JsonDecoder\Attributes;

use Attribute;
use Karriere\JsonDecoder\Bindings\FieldBinding;

#[Attribute]
class JsonClass implements AttributeInterface
{
    public function __construct(public string $className, public ?string $attribute = null)
    {
    }

    public function getBinding(string $propertyName)
    {
        $attribute = $this->attribute ?? $propertyName;

        return new FieldBinding($propertyName, $attribute, $this->className);
    }
}
