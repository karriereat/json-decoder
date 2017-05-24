<?php

namespace Karriere\JsonDecoder;

class PropertyAccessor
{
    /**
     * @var \ReflectionProperty
     */
    private $property;

    private $instance;

    private $isPrivateOrProtected;

    public function __construct(\ReflectionProperty $property, $instance, $isPrivateOrProtected = false)
    {
        $this->property = $property;
        $this->instance = $instance;
        $this->isPrivateOrProtected = $isPrivateOrProtected;
    }

    public function set($value)
    {
        if (!$this->isPrivateOrProtected) {
            $this->instance->{$this->property->getName()} = $value;
        } else {
            $this->property->setAccessible(true);
            $this->property->setValue($this->instance, $value);
            $this->property->setAccessible(false);
        }
    }
}
