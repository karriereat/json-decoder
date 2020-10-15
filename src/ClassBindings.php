<?php

namespace Karriere\JsonDecoder;

use Exception;
use Karriere\JsonDecoder\Bindings\AliasBinding;
use Karriere\JsonDecoder\Bindings\CallbackBinding;
use Karriere\JsonDecoder\Bindings\RawBinding;
use Karriere\JsonDecoder\Exceptions\InvalidBindingException;
use Karriere\JsonDecoder\Exceptions\JsonValueException;
use ReflectionProperty;

class ClassBindings
{
    /**
     * @var array
     */
    private $bindings = [];

    /**
     * @var array
     */
    private $callbackBindings = [];

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
     * @param mixed $instance
     *
     * @return mixed
     *
     * @throws JsonValueException
     */
    public function decode(array $data, $instance)
    {
        $jsonFieldNames = array_keys($data);

        foreach ($jsonFieldNames as $fieldName) {
            if ($this->hasBinding($fieldName)) {
                $binding  = $this->bindings[$fieldName];
                $property = Property::create($instance, $this->bindings[$fieldName]->property());
                $this->handleBinding($binding, $property, $data);
            } elseif (!$this->hasCallbackBinding($fieldName)) { // callback bindings are handled below
                if ($this->jsonDecoder->shouldAutoCase()) {
                    $property = $this->autoCase($fieldName, $instance);

                    if (!is_null($property)) {
                        $binding = new AliasBinding($property->getName(), $fieldName);
                        $binding->bind($this->jsonDecoder, $data, $property);
                        continue;
                    }
                }

                $property = Property::create($instance, $fieldName);
                $this->handleRaw($property, $data);
            }
        }

        foreach ($this->callbackBindings as $propertyName => $binding) {
            $property = Property::create($instance, $propertyName);
            $this->handleBinding($binding, $property, $data);
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
        } elseif ($binding instanceof CallbackBinding) {
            $this->callbackBindings[$binding->property()] = $binding;
        } else {
            $this->bindings[$binding->jsonField()] = $binding;
        }
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

    public function hasCallbackBinding(string $propertyName): bool
    {
        return array_key_exists($propertyName, $this->callbackBindings);
    }

    /**
     * validates and executes the found binding on the given property.
     *
     * @param Binding  $binding  the binding to execute
     * @param Property $property the property the binding is executed on
     * @param mixed    $data     the actual json array data
     *
     * @return void
     *
     * @throws JsonValueException if the binding validation fails
     */
    private function handleBinding(Binding $binding, Property $property, $data)
    {
        if (!$binding->validate($data)) {
            throw new JsonValueException($property->getName());
        }

        $binding->bind($this->jsonDecoder, $data, $property);
    }

    /**
     * builds a raw binding and executes it on the given property.
     *
     * @param Property $property the property to execute the binding on
     * @param mixed    $data     the actual json array data
     *
     * @return void
     */
    private function handleRaw(Property $property, $data)
    {
        (new RawBinding($property->getName()))->bind($this->jsonDecoder, $data, $property);
    }

    /**
     * creates the property variants and checks if one of them is an actual property of the instance.
     *
     * @param string $jsonField the json field name used for creating the variants
     * @param mixed  $instance  the class instance
     */
    private function autoCase(string $jsonField, $instance): ?Property
    {
        $variants = array_filter(
            [
                $this->snakeToCamelCase($jsonField),
                $this->kebapToCamelCase($jsonField),
            ],
            function ($variant) use ($jsonField) {
                return $variant !== $jsonField;
            }
        );

        foreach ($variants as $variant) {
            try {
                new ReflectionProperty($instance, $variant);

                return Property::create($instance, $variant);
            } catch (Exception $ignored) {
            }
        }

        return null;
    }

    /**
     * converts the given snake case input to camel case.
     *
     * @param string $input snake case input
     *
     * @return string
     */
    private function snakeToCamelCase(string $input)
    {
        $fn = function ($c) {
            return strtoupper($c[1]);
        };

        return preg_replace_callback('/_([a-z])/', $fn, strtolower($input));
    }

    /**
     * converts the given kebap case input to camel case.
     *
     * @param string $input kebap case input
     *
     * @return string
     */
    private function kebapToCamelCase(string $input)
    {
        $output    = str_replace('-', '', ucwords($input, '-'));
        $output[0] = strtolower($output[0]);

        return $output;
    }
}
