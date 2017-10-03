<?php

namespace tests\specs\Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Bindings\ArrayBinding;
use Karriere\JsonDecoder\Exceptions\JsonValueException;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\PropertyAccessor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ArrayBindingSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('property', 'field', Sample::class);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ArrayBinding::class);
    }

    public function it_should_return_the_property_name()
    {
        $this->property()->shouldReturn('property');
    }

    public function it_should_throw_an_exception_if_json_field_does_not_exist_and_is_required(JsonDecoder $jsonDecoder, PropertyAccessor $propertyAccessor)
    {
        $this->beConstructedWith('property', 'field', Sample::class, true);

        $this->shouldThrow(JsonValueException::class)->duringBind($jsonDecoder, [], $propertyAccessor);
    }

    public function it_should_not_set_value_when_json_field_does_not_exist_and_is_not_required(JsonDecoder $jsonDecoder, PropertyAccessor $propertyAccessor)
    {
        $jsonDecoder->decodeArray(Argument::any())->shouldNotBeCalled();

        $this->bind($jsonDecoder, [], $propertyAccessor);
    }

    public function it_should_decode_the_array(JsonDecoder $jsonDecoder, PropertyAccessor $propertyAccessor)
    {
        $jsonDecoder->decodeArray([], Sample::class)->shouldBeCalledTimes(2);

        $this->bind($jsonDecoder, ['field' => [[], []]], $propertyAccessor);
    }

    public function it_should_ignore_null_values(JsonDecoder $jsonDecoder, PropertyAccessor $propertyAccessor)
    {
        $jsonDecoder->decodeArray(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->bind($jsonDecoder, ['field' => null], $propertyAccessor);
    }
}

class Sample
{
    public $property;
}
