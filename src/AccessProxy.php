<?php

namespace Karriere\JsonDecoder;

use ReflectionProperty;

class AccessProxy
{
    /**
     * @var ReflectionProperty
     */
    private $property;

    private $instance;

    public function __construct(ReflectionProperty $property, $instance)
    {
        $this->property = $property;
        $this->instance = $instance;
    }

    public function __set($name, $value)
    {
        if ($name === $this->property->getName()) {
            $this->property->setAccessible(true);
            $this->property->setValue($this->instance, $value);
            $this->property->setAccessible(false);
        }
    }
}
