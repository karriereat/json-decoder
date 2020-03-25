<?php

namespace Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Binding;

class CallbackBinding extends Binding
{
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
        parent::__construct($property, null, null, false);
        $this->callback = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($jsonData): bool
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
}
