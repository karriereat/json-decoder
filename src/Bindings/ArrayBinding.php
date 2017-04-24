<?php

namespace Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Binding;
use Karriere\JsonDecoder\Exceptions\JsonValueException;
use Karriere\JsonDecoder\JsonDecoder;

class ArrayBinding implements Binding
{
    /**
     * @var string
     */
    private $property;

    /**
     * @var string
     */
    private $jsonField;

    /**
     * @var string
     */
    private $type;

    /**
     * ArrayBinding constructor.
     *
     * @param string $property the property to bind to
     * @param string $jsonField the json field
     * @param string $type the desired type of the property
     */
    public function __construct($property, $jsonField, $type)
    {
        $this->property = $property;
        $this->jsonField = $jsonField;
        $this->type = $type;
    }

    /**
     * executes the defined binding method on the class instance
     *
     * @param JsonDecoder $jsonDecoder
     * @param array $jsonData
     * @param mixed $instance the class instance to bind to
     * @return mixed
     * @throws JsonValueException if given json field is not available
     */
    public function bind($jsonDecoder, $jsonData, $instance)
    {
        if (!array_key_exists($this->jsonField, $jsonData)) {
            throw new JsonValueException(
                sprintf('the value "%s" for property "%s" does not exist', $this->jsonField, $this->property)
            );
        }

        $data = $jsonData[$this->jsonField];
        $values = [];

        foreach ($data as $item) {
            $values[] = $jsonDecoder->decodeArray($item, $this->type);
        }

        $instance->{$this->property} = $values;
    }

    /**
     * @return string the name of the property to bind
     */
    public function property()
    {
        return $this->property;
    }
}
