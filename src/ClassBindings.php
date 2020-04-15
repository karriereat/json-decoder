<?php

namespace Karriere\JsonDecoder;

use Karriere\JsonDecoder\Bindings\RawBinding;
use Karriere\JsonDecoder\Exceptions\InvalidBindingException;
use Karriere\JsonDecoder\Exceptions\JsonValueException;
use Karriere\JsonDecoder\Property;

class ClassBindings
{
    /**
     * @var array
     */
    private $bindings = [];

    /**
     * @var JsonDecoder
     */
    private $jsonDecoder;

    public function __construct(JsonDecoder $jsonDecoder)
    {
        $this->jsonDecoder = $jsonDecoder;
    }

    /**
     * decodes all available json fields into the given class instance.
     *
     * @param array $data
     * @param mixed $instance
     *
     * @return mixed
     */
    public function decode(array $data, $instance)
    {
        foreach (array_keys($data) as $fieldName) {
            if ($this->hasBinding($fieldName)) {
                $binding = $this->bindings[$fieldName];
                $property = Property::create($instance, $this->bindings[$fieldName]->property());
                $this->handleBinding($binding, $property, $data);
            } else {
                $property = Property::create($instance, $fieldName);
                $this->handleRaw($property, $data);
            }
        }

        return $instance;
    }

    /**
     * @param Binding $binding
     *
     * @throws InvalidBindingException
     */
    public function register($binding)
    {
        if (!$binding instanceof Binding) {
            throw new InvalidBindingException();
        }

        $this->bindings[$binding->jsonField()] = $binding;
    }

    /**
     * checks for a binding for the given property.
     *
     * @param string $property
     *
     * @return bool
     */
    public function hasBinding($property)
    {
        return array_key_exists($property, $this->bindings);
    }

    /**
     * validates and executes the found binding on the given property
     *
     * @param Binding $binding      the binding to execute
     * @param Property $property    the property the binding is executed on
     * @param mixed $data           the actual json array data
     *
     * @return void
     *
     * @throws JsonValueException   if the binding validation fails
     */
    private function handleBinding(Binding $binding, Property $property, $data)
    {
        if (!$binding->validate($data)) {
            throw new JsonValueException($property->getName());
        }

        $binding->bind($this->jsonDecoder, $data, $property);
    }

    /**
     * builds a raw binding and executes it on the given property
     * @param Property $property the property to execute the binding on
     * @param mixed $data        the actual json array data
     *
     * @return void
     */
    private function handleRaw(Property $property, $data)
    {
        (new RawBinding($property->getName()))->bind($this->jsonDecoder, $data, $property);
    }
}
