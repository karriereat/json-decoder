<?php

namespace Karriere\JsonDecoder;

use Karriere\JsonDecoder\Models\Field;

abstract class Transformer
{
    private $fields = [];
    private $arrayFields = [];

    /**
     * @var JsonDecoder
     */
    private $jsonDecoder;

    /**
     * add field to property bindings
     *
     * @return void
     */
    abstract public function bind();

    /**
     * @return string the full qualified class name that the transformer transforms
     */
    abstract public function transforms();

    public function initialize($jsonDecoder)
    {
        $this->jsonDecoder = $jsonDecoder;
        $this->bind();
    }

    public function decode($data, $instance)
    {
        foreach ($data as $key => $value) {
            if ($this->hasFieldBinding($key)) {
                $this->handleField($key, $value, $instance);
            } elseif ($this->hasArrayFieldBinding($key)) {
                $this->handleArray($key, $value, $instance);
            } else {
                $this->handleRaw($key, $value, $instance);
            }
        }

        return $instance;
    }

    /**
     * adds a json field binding to the given class property
     *
     * @param $jsonField string the json field name
     * @param $classProperty string the class property
     * @param string|null $type the type of the value to bind
     */
    protected function bindField($jsonField, $classProperty, $type = null)
    {
        $this->fields[$jsonField] = new Field($jsonField, $classProperty, $type);
    }

    /**
     * adds a json array binding to the given class property
     *
     * @param $jsonField string the json field name
     * @param $classProperty string the class property
     * @param string|null $type the type of the value to bind
     */
    protected function bindArray($jsonField, $classProperty, $type = null)
    {
        $this->arrayFields[$jsonField] = new Field($jsonField, $classProperty, $type);
    }

    protected function hasFieldBinding($jsonField)
    {
        return array_key_exists($jsonField, $this->fields);
    }

    protected function hasArrayFieldBinding($jsonField)
    {
        return array_key_exists($jsonField, $this->arrayFields);
    }

    protected function handleField($key, $value, $instance)
    {
        /** @var Field $field */
        $field = $this->fields[$key];
        $property = $field->property();

        if ($field->hasType()) {
            $value = $this->jsonDecoder->decode($value, $field->type());
        }

        $this->handleRaw($property, $value, $instance);
    }

    protected function handleArray($key, $value, $instance)
    {
        /** @var Field $field */
        $field = $this->arrayFields[$key];
        $property = $field->property();

        if ($field->hasType()) {
            $values = [];

            foreach ($value as $arrayValue) {
                $values[] = $this->jsonDecoder->decode($arrayValue, $field->type());
            }
        } else {
            $values = $value;
        }

        $this->handleRaw($property, $values, $instance);
    }

    protected function handleRaw($property, $value, $instance)
    {
        if (property_exists($instance, $property)) {
            $instance->{$property} = $value;
        }
    }
}
