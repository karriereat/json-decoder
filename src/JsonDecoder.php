<?php

namespace Karriere\JsonDecoder;

use Karriere\JsonDecoder\Bindings\DateTimeBinding;
use Karriere\JsonDecoder\Bindings\FieldBinding;
use Karriere\JsonDecoder\Bindings\RawBinding;
use Karriere\JsonDecoder\Exceptions\InvalidBindingException;
use Karriere\JsonDecoder\Exceptions\InvalidJsonException;
use Karriere\JsonDecoder\Exceptions\JsonValueException;
use Karriere\JsonDecoder\Exceptions\NotExistingRootException;
use PhpDocReader\PhpDocReader;
use ReflectionClass;

class JsonDecoder
{
    /**
     * @var array
     */
    private $transformers = [];

    /**
     * @var bool
     */
    private $shouldAutoCase = false;

    public function __construct(bool $shouldAutoCase = false)
    {
        $this->shouldAutoCase = $shouldAutoCase;
    }

    /**
     * registers the given transformer.
     */
    public function register(Transformer $transformer)
    {
        $this->transformers[$transformer->transforms()] = $transformer;
    }

    /**
     * scans the given class for annotated properties and creates the transformer for it
     * at the moment the scanner can detect custom classes and DateTime objects.
     *
     * @param string $class the class to check
     *
     * @return void
     */
    public function scanAndRegister(string $class)
    {
        $bindings = $this->scan($class);

        if (!empty($bindings)) {
            $transformer = $this->createTransformer($class, $bindings);
            $this->register($transformer);
        }
    }

    /**
     * Decodes the given JSON string into an instance of the given class type.
     *
     * @param string      $json      the input JSON string
     * @param string      $classType the class type of the decoded object
     * @param string|null $root      the root element to decode, if not defined the whole decoded json object will be decoded
     *
     * @return mixed the instance of the given class type
     *
     * @throws InvalidJsonException
     * @throws NotExistingRootException
     * @throws JsonValueException
     * @throws InvalidBindingException
     */
    public function decode(string $json, string $classType, string $root = null)
    {
        return $this->decodeArray($this->parseJson($json, $root), $classType);
    }

    /**
     * Decodes the given JSON string into multiple instances of the given class type.
     *
     * @param string      $json      the input JSON string
     * @param string      $classType the class type of the decoded objects
     * @param string|null $root      the root element to decode, if not defined the whole decoded json object will be decoded
     *
     * @return array the list of instances decoded for the given class type
     *
     * @throws InvalidJsonException
     * @throws NotExistingRootException
     * @throws JsonValueException
     * @throws InvalidBindingException
     */
    public function decodeMultiple(string $json, string $classType, string $root = null)
    {
        $data = $this->parseJson($json, $root);

        return array_map(
            function ($element) use ($classType) {
                return $this->decodeArray($element, $classType);
            },
            $data
        );
    }

    /**
     * decodes the given array data into an instance of the given class type.
     *
     * @param $jsonArrayData array
     * @param $classType string
     *
     * @return mixed an instance of $classType
     */
    public function decodeArray($jsonArrayData, $classType)
    {
        $instance = new $classType();

        if (array_key_exists($classType, $this->transformers)) {
            $instance = $this->transform($this->transformers[$classType], $jsonArrayData, $instance);
        } else {
            $instance = $this->transformRaw($jsonArrayData, $instance);
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
     * @param Transformer $transformer   the transformer to use
     * @param array       $jsonArrayData the actual json data
     * @param mixed       $instance      the class instance to bind to
     *
     * @return mixed|null
     *
     * @throws JsonValueException if the json data is not valid
     */
    protected function transform(Transformer $transformer, ?array $jsonArrayData, $instance)
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
     * @param mixed $jsonArrayData the actual json data
     * @param mixed $instance      the class instance to bind to
     *
     * @return mixed|null
     *
     * @throws JsonValueException if the json data is not valid
     */
    protected function transformRaw($jsonArrayData, $instance)
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
     * @param string      $json the json string to parse
     * @param string|null $root the optional root key
     *
     * @return mixed the json data in array format
     *
     * @throws InvalidJsonException     if the json data cannot be parsed
     * @throws NotExistingRootException if the defined root key does not exist
     */
    private function parseJson(string $json, string $root = null)
    {
        $jsonData = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidJsonException();
        }

        if (!is_null($root)) {
            if (!array_key_exists($root, $jsonData)) {
                throw new NotExistingRootException($root);
            }

            $jsonData = $jsonData[$root];
        }

        return $jsonData;
    }

    /**
     * scans the given class and creates bindings for annotated properties.
     *
     * @param string $class the class to scan
     *
     * @return array the list of generated bindings
     *
     * @throws ReflectionException
     */
    private function scan(string $class)
    {
        $bindings        = [];
        $reflectionClass = new ReflectionClass($class);

        foreach ($reflectionClass->getProperties() as $property) {
            $reader = new PhpDocReader();

            $propertyName = $property->getName();
            $propertyType = $reader->getPropertyClass($property);

            if (!is_null($propertyType)) {
                if ($propertyType === 'DateTime') {
                    $bindings[] = new DateTimeBinding($propertyName, $propertyName);
                } else {
                    $bindings[] = new FieldBinding($propertyName, $propertyName, $propertyType);
                }
            } else {
                $bindings[] = new RawBinding($propertyName);
            }
        }

        return $bindings;
    }

    /**
     * creates the transformer instance for the given class and generated bindings.
     *
     * @param string $class    the class the transformer can handle
     * @param array  $bindings the bindings that need to be registered
     */
    private function createTransformer(string $class, array $bindings): Transformer
    {
        return new class($class, $bindings) implements Transformer {
            public function __construct($class, $bindings)
            {
                $this->class    = $class;
                $this->bindings = $bindings;
            }

            public function register(ClassBindings $classBindings)
            {
                foreach ($this->bindings as $binding) {
                    $classBindings->register($binding);
                }
            }

            public function transforms()
            {
                return $this->class;
            }
        };
    }
}
