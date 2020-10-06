<?php

namespace Karriere\JsonDecoder;

use Exception;
use ReflectionProperty;

class Property
{
    /**
     * @var mixed
     */
    private $instance;

    /**
     * @var string
     */
    private $propertyName;

    /**
     * @var ReflectionProperty
     */
    private $property;

    /**
     * creates the property instance.
     *
     * @param mixed  $instance     the class instance the property lives in
     * @param string $propertyName the name of the property
     *
     * @return static
     */
    public static function create($instance, string $propertyName)
    {
        $property = null;
        try {
            $property = new ReflectionProperty($instance, $propertyName);
            $property->setAccessible(true);
        } catch (Exception $ignored) {
        }

        return new static($instance, $propertyName, $property);
    }

    private function __construct($instance, string $propertyName, ReflectionProperty $property = null)
    {
        $this->instance     = $instance;
        $this->propertyName = $propertyName;
        $this->property     = $property;
    }

    /**
     * sets the value to the property.
     *
     * @param mixed $value
     *
     * @return void
     */
    public function set($value)
    {
        if (is_null($this->property)) {
            $this->instance->{$this->propertyName} = $value;
        } else {
            $property = new ReflectionProperty(get_class($this->instance), $this->propertyName);
            $property->setAccessible(true);
            $property->setValue($this->instance, $value);
        }
    }

    /**
     * gets the name ot the property.
     *
     * @return string
     */
    public function getName()
    {
        return $this->propertyName;
    }
}
