<?php

namespace tests\specs\Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Bindings\RawBinding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\PropertyAccessor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RawBindingSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('property');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(RawBinding::class);
    }

    public function it_should_return_the_property_name()
    {
        $this->property()->shouldReturn('property');
    }

    public function it_should_not_do_anything_if_json_field_does_not_exist(JsonDecoder $jsonDecoder, PropertyAccessor $propertyAccessor)
    {
        $propertyAccessor->set(Argument::any())->shouldNotBeCalled();

        $this->bind($jsonDecoder, [], $propertyAccessor);
    }

    public function it_should_assign_the_json_value_to_the_property(JsonDecoder $jsonDecoder, PropertyAccessor $propertyAccessor)
    {
        $propertyAccessor->set('value')->shouldBeCalled();

        $this->bind($jsonDecoder, ['property' => 'value'], $propertyAccessor);
    }
}

class RawBindingSample
{
    public $property;
}
