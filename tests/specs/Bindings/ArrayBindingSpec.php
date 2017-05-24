<?php

namespace tests\specs\Karriere\JsonDecoder\Bindings;

use Karriere\JsonDecoder\Bindings\ArrayBinding;
use Karriere\JsonDecoder\Exceptions\JsonValueException;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\PropertyAccessor;
use PhpSpec\ObjectBehavior;

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

    public function it_should_throw_anc_exception_if_json_field_does_not_exist(JsonDecoder $jsonDecoder, PropertyAccessor $propertyAccessor)
    {
        $this->shouldThrow(JsonValueException::class)->duringBind($jsonDecoder, [], $propertyAccessor);
    }

    public function it_should_decode_the_array(JsonDecoder $jsonDecoder, PropertyAccessor $propertyAccessor)
    {
        $jsonDecoder->decodeArray([], Sample::class)->shouldBeCalledTimes(2);

        $this->bind($jsonDecoder, ['field' => [[], []]], $propertyAccessor);
    }
}

class Sample
{
    public $property;
}
