<?php

namespace Karriere\JsonDecoder\Attributes;

use Attribute;
use Karriere\JsonDecoder\Bindings\AliasBinding;

#[Attribute]
class JsonAlias implements AttributeInterface
{
    public function __construct(public string $property, public bool $isRequired = false)
    {
    }

    public function getBinding(string $propertyName)
    {
        return new AliasBinding($propertyName, $this->property, $this->isRequired);
    }
}
