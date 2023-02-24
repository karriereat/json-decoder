<?php

namespace Karriere\JsonDecoder;

use Exception;
use Karriere\JsonDecoder\Bindings\AliasBinding;
use Karriere\JsonDecoder\Bindings\CallbackBinding;
use Karriere\JsonDecoder\Bindings\RawBinding;
use Karriere\JsonDecoder\Exceptions\JsonValueException;
use ReflectionException;
use ReflectionProperty;

class ClassBindings
{
    /**
     * @var array<string, Binding>
     */
    private array $bindings = [];

    /**
     * @var array<string, CallbackBinding>
     */
    private array $callbackBindings = [];

    public function __construct(private JsonDecoder $jsonDecoder)
    {
    }

    /**
     * decodes all available json fields into the given class instance.
     *
     * @throws JsonValueException
     * @throws ReflectionException
     */
    public function decode(array $data, object $instance): mixed
    {
        $jsonFieldNames = array_keys($data);

        foreach ($jsonFieldNames as $fieldName) {
            if ($this->hasBinding($fieldName)) {
                $binding = $this->bindings[$fieldName];
                $property = Property::create($instance, $this->bindings[$fieldName]->property());
                $this->handleBinding($binding, $property, $data);
            } elseif (! $this->hasCallbackBinding($fieldName)) { // callback bindings are handled below
                if ($this->jsonDecoder->shouldAutoCase()) {
                    $property = $this->autoCase($fieldName, $instance);

                    if (! is_null($property)) {
                        $binding = new AliasBinding($property->getName(), $fieldName);
                        $binding->bind($this->jsonDecoder, $property, $data);

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

    public function register(Binding $binding): void
    {
        if ($binding instanceof CallbackBinding) {
            $this->callbackBindings[$binding->property()] = $binding;
        } else {
            $this->bindings[$binding->jsonField()] = $binding;
        }
    }

    public function hasBinding(string $property): bool
    {
        return array_key_exists($property, $this->bindings);
    }

    public function hasCallbackBinding(string $propertyName): bool
    {
        return array_key_exists($propertyName, $this->callbackBindings);
    }

    /**
     * @throws JsonValueException
     */
    private function handleBinding(Binding $binding, Property $property, array $data): void
    {
        if (! $binding->validate($data)) {
            throw new JsonValueException($property->getName());
        }

        $binding->bind($this->jsonDecoder, $property, $data);
    }

    /**
     * builds a raw binding and executes it on the given property.
     */
    private function handleRaw(Property $property, array $data): void
    {
        (new RawBinding($property->getName()))->bind($this->jsonDecoder, $property, $data);
    }

    /**
     * creates the property variants and checks if one of them is an actual property of the instance.
     */
    private function autoCase(string $jsonField, object $instance): ?Property
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
            } catch (Exception) {
            }
        }

        return null;
    }

    /**
     * converts the given snake case input to camel case.
     */
    private function snakeToCamelCase(string $input): string
    {
        $result = preg_replace_callback('/_([a-z])/', fn ($c) => strtoupper($c[1]), strtolower($input));

        return $result ?: $input;
    }

    /**
     * converts the given kebap case input to camel case.
     */
    private function kebapToCamelCase(string $input): string
    {
        $output = str_replace('-', '', ucwords($input, '-'));
        $output[0] = strtolower($output[0]);

        return $output;
    }
}
