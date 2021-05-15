<?php

namespace Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Binding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Property;

class StaticCallbackBinding extends Binding
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * StaticCallbackBinding constructor.
     */
    public function __construct(string $property, array $callback)
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
        $property->set(call_user_func($this->callback, $jsonData, $jsonDecoder));
    }
}
