<?php

namespace Karriere\JsonDecoder\Tests\Bindings;

use DateTime;
use Karriere\JsonDecoder\Bindings\DateTimeBinding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Property;
use Karriere\JsonDecoder\Tests\Fakes\Person;
use PHPUnit\Framework\TestCase;

class DateTimeBindingTest extends TestCase
{
    /** @test */
    public function it_validates_successfully()
    {
        $binding = new DateTimeBinding('date', 'date');

        $this->assertTrue($binding->validate(['date' => '2020-01-01T12:00:00+00:00']));
    }

    /** @test */
    public function it_fails_on_validation_of_a_required_property_with_an_invalid_date_format()
    {
        $binding = new DateTimeBinding('date', 'date', true);

        $this->assertFalse($binding->validate(['date' => 'invalid']));
    }

    /** @test */
    public function it_fails_on_validation_of_a_not_required_property_with_an_invalid_date_format()
    {
        $binding = new DateTimeBinding('date', 'date', false);

        $this->assertFalse($binding->validate(['date' => 'invalid']));
    }

    /** @test */
    public function it_fails_on_validation_for_a_not_set_field_that_is_required()
    {
        $binding = new DateTimeBinding('date', 'date', true);
        $this->assertFalse($binding->validate([]));
    }

    /** @test */
    public function it_succeeds_on_validation_for_a_not_set_field_that_is_not_required()
    {
        $binding = new DateTimeBinding('date', 'date', false);
        $this->assertTrue($binding->validate([]));
    }

    /** @test */
    public function it_binds_an_atom_datetime()
    {
        $binding = new DateTimeBinding('date', 'date');
        $person = new Person();
        $property = Property::create($person, 'date');

        $binding->bind(new JsonDecoder(), ['date' => '2020-01-01T12:00:00+00:00'], $property);

        $expected = DateTime::createFromFormat(DateTime::ATOM, '2020-01-01T12:00:00+00:00');

        $this->assertEquals($expected, $person->date);
    }

    /** @test */
    public function it_ignores_an_empty_datetime_value()
    {
        $binding = new DateTimeBinding('date', 'date');
        $person = new Person();
        $property = Property::create($person, 'date');

        $binding->bind(new JsonDecoder(), ['date' => ''], $property);

        $this->assertFalse(property_exists($person, 'date'));
    }

    /** @test */
    public function it_ignores_an_invalid_datetime_value()
    {
        $binding = new DateTimeBinding('date', 'date');
        $person = new Person();
        $property = Property::create($person, 'date');

        $binding->bind(new JsonDecoder(), ['date' => 'invalid'], $property);

        $this->assertFalse(property_exists($person, 'date'));
    }
}
