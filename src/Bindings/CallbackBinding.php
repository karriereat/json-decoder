<?php

namespace Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Binding;
use Karriere\JsonDecoder\JsonDecoder;

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
     * {@inheritdoc}
     */
    public function validate($jsonData) : bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function bind($jsonDecoder, $jsonData, $propertyAccessor)
    {
        $propertyAccessor->set($this->callback->__invoke($jsonData, $jsonDecoder));
    }

    /**
     * {@inheritdoc}
     */
    public function property()
    {
        return $this->property;
    }
}
