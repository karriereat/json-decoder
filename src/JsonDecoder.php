<?php

namespace Karriere\JsonDecoder;

use Karriere\JsonDecoder\Bindings\DateTimeBinding;
use Karriere\JsonDecoder\Bindings\FieldBinding;
use Karriere\JsonDecoder\Bindings\RawBinding;
use Karriere\JsonDecoder\Exceptions\InvalidJsonException;
use Karriere\JsonDecoder\Exceptions\JsonValueException;
use Karriere\JsonDecoder\Exceptions\NotExistingRootException;
use PhpDocReader\AnnotationException;
use PhpDocReader\PhpDocReader;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;

class JsonDecoder
{
    /**
     * @var array<class-string, Transformer>
     */
    private array $transformers = [];

    public function __construct(private bool $shouldAutoCase = false)
    {
    }

    /**
     * registers the given transformer.
     */
    public function register(Transformer $transformer): void
    {
        $this->transformers[$transformer->transforms()] = $transformer;
    }

    /**
     * scans the given class for annotated properties and creates the transformer for it
     * at the moment the scanner can detect custom classes and DateTime objects.
     *
     * @param  class-string  $class
     *
     * @throws AnnotationException
     * @throws ReflectionException
     */
    public function scanAndRegister(string $class): void
    {
        $bindings = $this->scan($class);

        if (! empty($bindings)) {
            $transformer = $this->createTransformer($class, $bindings);
            $this->register($transformer);
        }
    }

    /**
     * Decodes the given JSON string into an instance of the given class type.
     *
     * @throws InvalidJsonException
     * @throws NotExistingRootException
     * @throws JsonValueException
     * @throws ReflectionException
     */
    public function decode(string $json, string $classType, ?string $root = null): mixed
    {
        return $this->decodeArray($this->parseJson($json, $root), $classType);
    }

    /**
     * Decodes the given JSON string into multiple instances of the given class type.
     *
     * @throws InvalidJsonException
     * @throws NotExistingRootException
     * @throws JsonValueException
     * @throws ReflectionException
     */
    public function decodeMultiple(string $json, string $classType, ?string $root = null): array
    {
        $data = $this->parseJson($json, $root);

        return array_map(
            function ($element) use ($classType) {
                return is_array($element) ? $this->decodeArray($element, $classType) : $this->decodeArray(null, $classType);
            },
            $data
        );
    }

    /**
     * decodes the given array data into an instance of the given class type.
     *
     * @throws JsonValueException
     * @throws ReflectionException
     */
    public function decodeArray(?array $jsonArrayData, string $classType): mixed
    {
        $instance = new $classType();

        if (array_key_exists($classType, $this->transformers)) {
            $instance = $this->transform($this->transformers[$classType], $instance, $jsonArrayData ?? []);
        } else {
            $instance = $this->transformRaw($instance, $jsonArrayData ?? []);
        }

        return $instance;
    }

    public function shouldAutoCase(): bool
    {
        return $this->shouldAutoCase;
    }

    /**
     * transforms the given json data by using the found transformer.
     *
     * @throws JsonValueException
     * @throws ReflectionException
     */
    protected function transform(Transformer $transformer, object $instance, array $jsonArrayData = []): mixed
    {
        if (empty($jsonArrayData)) {
            return null;
        }

        $classBindings = new ClassBindings($this);
        $transformer->register($classBindings);

        return $classBindings->decode($jsonArrayData, $instance);
    }

    /**
     * transforms the given data with raw bindings.
     *
     * @throws JsonValueException
     * @throws ReflectionException
     */
    protected function transformRaw(object $instance, array $jsonArrayData): mixed
    {
        if (empty($jsonArrayData)) {
            return null;
        }

        $classBindings = new ClassBindings($this);

        return $classBindings->decode($jsonArrayData, $instance);
    }

    /**
     * parses the given json string and eventually selects the defined root key.
     *
     * @throws InvalidJsonException     if the json data cannot be parsed
     * @throws NotExistingRootException if the defined root key does not exist
     */
    private function parseJson(string $json, ?string $root = null): array
    {
        $jsonData = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidJsonException();
        }

        if (! is_array($jsonData)) {
            return [];
        }

        if (! is_null($root)) {
            if (! array_key_exists($root, $jsonData)) {
                throw new NotExistingRootException($root);
            }

            $jsonData = $jsonData[$root];
        }

        return $jsonData;
    }

    /**
     * scans the given class and creates bindings for annotated properties.
     *
     * @param  class-string  $class
     * @return array<int, Binding>
     *
     * @throws AnnotationException
     * @throws ReflectionException
     */
    private function scan(string $class): array
    {
        $bindings = [];
        $reflectionClass = new ReflectionClass($class);

        foreach ($reflectionClass->getProperties() as $property) {
            $reader = new PhpDocReader();

            $propertyName = $property->getName();

            if ($property->hasType() && $property->getType() instanceof ReflectionNamedType) {
                $propertyType = ! $property->getType()->isBuiltin() ? $property->getType()->getName() : null;
            } else {
                $propertyType = $reader->getPropertyClass($property);
            }

            if (! is_null($propertyType)) {
                if ($propertyType === 'DateTime') {
                    $bindings[] = new DateTimeBinding(property: $propertyName, jsonField: $propertyName);
                } else {
                    $bindings[] = new FieldBinding(property: $propertyName, jsonField: $propertyName, type: $propertyType);
                }
            } else {
                $bindings[] = new RawBinding(property: $propertyName);
            }
        }

        return $bindings;
    }

    /**
     * creates the transformer instance for the given class and generated bindings.
     *
     * @param  class-string  $class
     * @param  array<int, Binding>  $bindings
     */
    private function createTransformer(string $class, array $bindings): Transformer
    {
        return new class ($class, $bindings) implements Transformer {
            /**
             * @param  class-string  $class
             * @param  array<int, Binding>  $bindings
             */
            public function __construct(private string $class, private array $bindings)
            {
            }

            public function register(ClassBindings $classBindings): void
            {
                foreach ($this->bindings as $binding) {
                    $classBindings->register($binding);
                }
            }

            /**
             * @return class-string
             */
            public function transforms(): string
            {
                return $this->class;
            }
        };
    }
}
