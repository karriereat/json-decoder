<?php

namespace tests\specs\Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Bindings\FieldBinding;
use Karriere\JsonDecoder\Exceptions\JsonValueException;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\PropertyAccessor;
use PhpSpec\ObjectBehavior;

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

    public function it_should_throw_an_exception_if_the_json_field_does_not_exist(JsonDecoder $jsonDecoder, PropertyAccessor $propertyAccessor)
    {
        $this->shouldThrow(JsonValueException::class)->duringBind($jsonDecoder, [], $propertyAccessor);
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
