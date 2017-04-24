<?php

namespace Karriere\JsonDecoder;

class JsonDecoder
{
    /**
     * @var array
     */
    private $transformers = [];

    private $decodePrivateProperties;

    private $decodeProtectedProperties;

    /**
     * JsonDecoder constructor.
     *
     * @param bool $decodePrivateProperties
     * @param bool $decodeProtectedProperties
     */
    public function __construct($decodePrivateProperties = false, $decodeProtectedProperties = false)
    {
        $this->decodePrivateProperties = $decodePrivateProperties;
        $this->decodeProtectedProperties = $decodeProtectedProperties;
    }

    /**
     * registers the given transformer.
     *
     * @param Transformer $transformer
     */
    public function register(Transformer $transformer)
    {
        $this->transformers[$transformer->transforms()] = $transformer;
    }

    public function decode($jsonString, $classType)
    {
        return $this->decodeArray(json_decode($jsonString, true), $classType);
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

    public function decodesPrivateProperties()
    {
        return $this->decodePrivateProperties;
    }

    public function decodesProtectedProperties()
    {
        return $this->decodeProtectedProperties;
    }

    private function transform($transformer, $jsonArrayData, $instance)
    {
        $classBindings = new ClassBindings($this);
        $transformer->register($classBindings);

        return $classBindings->decode($jsonArrayData, $instance);
    }

    protected function transformRaw($jsonArrayData, $instance)
    {
        foreach ($jsonArrayData as $key => $value) {
            if (property_exists($instance, $key)) {
                $instance->{$key} = $value;
            }
        }

        return $instance;
    }
}
