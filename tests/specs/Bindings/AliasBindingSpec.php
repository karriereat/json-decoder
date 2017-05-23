<?php

namespace tests\specs\Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Bindings\AliasBinding;
use Karriere\JsonDecoder\Exceptions\JsonValueException;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\PropertyAccessor;
use PhpSpec\ObjectBehavior;

class AliasBindingSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('property', 'field');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AliasBinding::class);
    }

    function it_should_return_bound_property()
    {
        $this->property()->shouldReturn('property');
    }

    function it_should_throw_an_exception_if_json_field_is_not_available(JsonDecoder $jsonDecoder)
    {
        $this->shouldThrow(JsonValueException::class)->duringBind($jsonDecoder, [], new \stdClass());
    }

    function it_should_set_the_json_value_to_the_property(JsonDecoder $jsonDecoder, PropertyAccessor $propertyAccessor)
    {
        $propertyAccessor->set('value')->shouldBeCalled();

        $this->bind($jsonDecoder, ['field' => 'value'], $propertyAccessor);
    }
}