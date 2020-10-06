<?php

namespace Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Binding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Property;

class CallbackBinding extends Binding
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * CallbackBinding constructor.
     */
    public function __construct(string $property, callable $callback)
    {
        parent::__construct($property, null, null, false);
        $this->callback = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $jsonData): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function bind(JsonDecoder $jsonDecoder, ?array $jsonData, Property $property)
    {
        $property->set($this->callback->__invoke($jsonData, $jsonDecoder));
    }
}
