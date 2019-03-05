<?php

namespace tests\specs\Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Bindings\AliasBinding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\PropertyAccessor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AliasBindingSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('property', 'field');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(AliasBinding::class);
    }

    public function it_should_return_bound_property()
    {
        $this->property()->shouldReturn('property');
    }

    public function it_should_succeed_on_binding_validation()
    {
        $this->validate(['field' => 'value'])->shouldReturn(true);
    }

    public function it_should_succeed_on_binding_validation_of_a_required_property()
    {
        $this->beConstructedWith('property', 'field', true);
        $this->validate(['field' => 'value'])->shouldReturn(true);
    }

    public function it_should_fail_on_binding_validation_when_a_required_property_is_missing()
    {
        $this->beConstructedWith('property', 'field', true);
        $this->validate([])->shouldReturn(false);
    }

    public function it_should_not_set_the_json_value_when_value_is_not_required(JsonDecoder $jsonDecoder, PropertyAccessor $propertyAccessor)
    {
        $propertyAccessor->set(Argument::any())->shouldNotBeCalled();

        $this->bind($jsonDecoder, [], $propertyAccessor);
    }

    public function it_should_set_the_json_value_to_the_property(JsonDecoder $jsonDecoder, PropertyAccessor $propertyAccessor)
    {
        $propertyAccessor->set('value')->shouldBeCalled();

        $this->bind($jsonDecoder, ['field' => 'value'], $propertyAccessor);
    }
}
