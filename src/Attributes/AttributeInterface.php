<?php

namespace Karriere\JsonDecoder\Attributes;

interface AttributeInterface
{
    /**
     * Gets the binding associated with the attribute.
     *
     * @return Karriere\JsonDecoder\Binding
     */
    public function getBinding(string $propertyName);
}
