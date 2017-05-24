<?php

namespace Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Binding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\PropertyAccessor;

class CallbackBinding implements Binding
{
    /**
     * @var string
     */
    private $property;

    /**
     * @var callable
     */
    private $callback;

    /**
     * CallbackBinding constructor.
     *
     * @param string   $property
     * @param callable $callback
     */
    public function __construct($property, $callback)
    {
        $this->property = $property;
        $this->callback = $callback;
    }

    /**
     * executes the defined binding method on the class instance.
     *
     * @param JsonDecoder      $jsonDecoder
     * @param mixed            $jsonData
     * @param PropertyAccessor $propertyAccessor the class instance to bind to
     *
     * @return mixed
     */
    public function bind($jsonDecoder, $jsonData, $propertyAccessor)
    {
        $propertyAccessor->set($this->callback->__invoke($jsonData));
    }

    /**
     * @return string the name of the property to bind
     */
    public function property()
    {
        return $this->property;
    }
}
