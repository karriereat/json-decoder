<?php

namespace Karriere\JsonDecoder\Attributes;

use Attribute;
use Karriere\JsonDecoder\Bindings\StaticCallbackBinding;

#[Attribute]
class JsonCallback implements AttributeInterface
{
    public function __construct(public string $callbackClass, public string $callbackMethod)
    {
    }

    public function getBinding(string $propertyName)
    {
        return new StaticCallbackBinding($propertyName, [$this->callbackClass, $this->callbackMethod]);
    }
}
