<?php

namespace tests\specs\Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Bindings\FieldBinding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\PropertyAccessor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FieldBindingSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('property', 'field', FieldBindingSample::class);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(FieldBinding::class);
    }

    public function it_should_return_the_property_name()
    {
        $this->property()->shouldReturn('property');
    }

    public function it_should_succeed_on_binding_validation()
    {
        $this->validate(['field' => 'value'])->shouldReturn(true);
    }

    public function it_should_succeed_on_binding_validation_of_a_required_property()
    {
        $this->beConstructedWith('property', 'field', FieldBindingSample::class, true);
        $this->validate(['field' => 'value'])->shouldReturn(true);
    }

    public function it_should_fail_on_binding_validation_when_a_required_property_is_missing()
    {
        $this->beConstructedWith('property', 'field', FieldBindingSample::class, true);
        $this->validate([])->shouldReturn(false);
    }

    public function it_should_not_set_value_if_the_json_field_does_not_exist_and_is_not_required(JsonDecoder $jsonDecoder, PropertyAccessor $propertyAccessor)
    {
        $propertyAccessor->set(Argument::any())->shouldNotBeCalled();

        $this->bind($jsonDecoder, [], $propertyAccessor);
    }

    public function it_should_bind_the_decoded_value(JsonDecoder $jsonDecoder, PropertyAccessor $propertyAccessor)
    {
        $jsonDecoder->decodeArray([], FieldBindingSample::class)->willReturn('data')->shouldBeCalled();
        $propertyAccessor->set('data')->shouldBeCalled();

        $this->bind($jsonDecoder, ['field' => []], $propertyAccessor);
    }
}

class FieldBindingSample
{
    public $property;
}
