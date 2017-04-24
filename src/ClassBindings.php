<?php

namespace Karriere\JsonDecoder;

use Karriere\JsonDecoder\Bindings\RawBinding;

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
     * decodes all available properties of the given class instance.
     *
     * @param array $data
     * @param midex $instance
     *
     * @return mixed
     */
    public function decode($data, $instance)
    {
        $reflectionClass = new \ReflectionClass($instance);
        $properties = $reflectionClass->getProperties();

        foreach ($properties as $property) {
            $needsProxy = false;

            if ($property->isPrivate()) {
                if ($this->jsonDecoder->decodesPrivateProperties()) {
                    $needsProxy = true;
                } else {
                    continue;
                }
            } else {
                if ($property->isProtected()) {
                    if ($this->jsonDecoder->decodesProtectedProperties()) {
                        $needsProxy = true;
                    } else {
                        continue;
                    }
                }
            }

            if ($needsProxy) {
                $decodeable = new AccessProxy($property, $instance);
            } else {
                $decodeable = $instance;
            }

            $propertyName = $property->getName();
            if ($this->hasBinding($propertyName)) {
                /** @var Binding $binding */
                $binding = $this->bindings[$propertyName];
                $binding->bind($this->jsonDecoder, $data, $decodeable);
            } else {
                $this->handleRaw($propertyName, $data, $decodeable);
            }
        }

        return $instance;
    }

    /**
     * @param Binding $binding
     */
    public function register($binding)
    {
        $this->bindings[$binding->property()] = $binding;
    }

    /**
     * checks for a binding for the given property.
     *
     * @param string $property
     *
     * @return bool
     */
    private function hasBinding($property)
    {
        return array_key_exists($property, $this->bindings);
    }

    private function handleRaw($property, $data, $instance)
    {
        (new RawBinding($property))->bind($this->jsonDecoder, $data, $instance);
    }
}
