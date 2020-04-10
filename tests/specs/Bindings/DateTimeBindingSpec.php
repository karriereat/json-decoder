<?php

namespace tests\specs\Karriere\JsonDecoder\Bindings;

use DateTime;
use Karriere\JsonDecoder\Bindings\DateTimeBinding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\PropertyAccessor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DateTimeBindingSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('property', 'field');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(DateTimeBinding::class);
    }

    public function it_should_return_the_property_name()
    {
        $this->property()->shouldReturn('property');
    }

    public function it_should_succeed_on_binding_validation()
    {
        $this->validate(['field' => '2018-02-01T12:00:00+00:00'])->shouldReturn(true);
    }

    public function it_should_fail_on_binding_validation_for_a_required_property_with_invalid_format()
    {
        $this->beConstructedWith('property', 'field', true);
        $this->validate(['field' => 'value'])->shouldReturn(false);
    }

    public function it_should_fail_on_binding_validation_for_a_not_required_property_with_invalid_format()
    {
        $this->validate(['field' => 'value'])->shouldReturn(false);
    }

    public function it_should_be_able_to_parse_an_atom_datetime(JsonDecoder $jsonDecoder, PropertyAccessor $propertyAccessor)
    {
        $expectedDateTime = DateTime::createFromFormat(DateTime::ATOM, '2020-01-01T12:00:00+00:00');
        $propertyAccessor->set($expectedDateTime)->shouldBeCalled();

        $this->bind($jsonDecoder, ['field' => '2020-01-01T12:00:00+00:00'], $propertyAccessor);
    }

    public function it_should_not_set_a_value_for_an_empty_datetime_string(JsonDecoder $jsonDecoder, PropertyAccessor $propertyAccessor)
    {
        $propertyAccessor->set(Argument::any())->shouldNotBeCalled();

        $this->bind($jsonDecoder, ['field' => ''], $propertyAccessor);
    }

    public function it_should_not_set_a_value_for_an_invalid_formatted_datetime_string(JsonDecoder $jsonDecoder, PropertyAccessor $propertyAccessor)
    {
        $propertyAccessor->set(Argument::any())->shouldNotBeCalled();

        $this->bind($jsonDecoder, ['field' => 'invalid'], $propertyAccessor);
    }
}
