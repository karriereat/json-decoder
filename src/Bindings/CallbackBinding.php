<?php

namespace Karriere\JsonDecoder\Bindings;

use Closure;
use Karriere\JsonDecoder\Binding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Property;

class CallbackBinding extends Binding
{
    /**
     * @var Closure
     */
    private $callback;

    /**
     * CallbackBinding constructor.
     *
     * @param string $property
     * @param Closure $callback
     */
    public function __construct(string $property, Closure $callback)
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
