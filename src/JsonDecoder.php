<?php

namespace Karriere\JsonDecoder;

class JsonDecoder
{
    private $transformers = [];

    public function __construct()
    {
    }

    public function register(Transformer $transformer)
    {
        $this->transformers[$transformer->transforms()] = $transformer;
        $transformer->initialize($this);
    }

    public function decode($jsonArrayData, $classType)
    {
        $instance = new $classType();

        if (array_key_exists($classType, $this->transformers)) {
            $instance = $this->transformers[$classType]->decode($jsonArrayData, $instance);
        } else {
            $instance = $this->transformRaw($jsonArrayData, $instance);
        }

        return $instance;
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
