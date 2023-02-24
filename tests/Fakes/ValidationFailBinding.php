<?php

namespace Karriere\JsonDecoder\Tests\Fakes;

use Karriere\JsonDecoder\Binding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Property;

class ValidationFailBinding extends Binding
{
    public function __construct(?string $property = null, ?string $jsonField = null)
    {
        parent::__construct($property ?? 'property', $jsonField ?? 'jsonField', 'type');
    }

    public function validate($jsonData): bool
    {
        return false;
    }

    public function bind(JsonDecoder $jsonDecoder, Property $property, array $jsonData = []): void
    {
    }
}
