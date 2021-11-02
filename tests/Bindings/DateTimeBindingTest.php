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
    public function itValidatesSuccessfully()
    {
        $binding = new DateTimeBinding('date', 'date');

        $this->assertTrue($binding->validate(['date' => '2020-01-01T12:00:00+00:00']));
    }

    /** @test */
    public function itFailsOnValidationOfARequiredPropertyWithAnInvalidDateFormat()
    {
        $binding = new DateTimeBinding('date', 'date', true);

        $this->assertFalse($binding->validate(['date' => 'invalid']));
    }

    /** @test */
    public function itFailsOnValidationOfANotRequiredPropertyWithAnInvalidDateFormat()
    {
        $binding = new DateTimeBinding('date', 'date', false);

        $this->assertFalse($binding->validate(['date' => 'invalid']));
    }

    /** @test */
    public function itFailsOnValidationForANotSetFieldThatIsRequired()
    {
        $binding = new DateTimeBinding('date', 'date', true);
        $this->assertFalse($binding->validate([]));
    }

    /** @test */
    public function itSucceedsOnValidationForANotSetFieldThatIsNotRequired()
    {
        $binding = new DateTimeBinding('date', 'date', false);
        $this->assertTrue($binding->validate([]));
    }

    /** @test */
    public function itSucceedsOnValidationForAnEmptyFieldThatIsNotRequired()
    {
        $binding = new DateTimeBinding('date', 'date', false);
        $this->assertTrue($binding->validate(['date' => '']));
    }

    /** @test */
    public function itBindsAnAtomDatetime()
    {
        $binding  = new DateTimeBinding('date', 'date');
        $person   = new Person();
        $property = Property::create($person, 'date');

        $binding->bind(new JsonDecoder(), ['date' => '2020-01-01T12:00:00+00:00'], $property);

        $expected = DateTime::createFromFormat(DateTime::ATOM, '2020-01-01T12:00:00+00:00');

        $this->assertEquals($expected, $person->date);
    }

    /** @test */
    public function itIgnoresAnEmptyDatetimeValue()
    {
        $binding  = new DateTimeBinding('date', 'date');
        $person   = new Person();
        $property = Property::create($person, 'date');

        $binding->bind(new JsonDecoder(), ['date' => ''], $property);

        $this->assertFalse(property_exists($person, 'date'));
    }

    /** @test */
    public function itIgnoresAnInvalidDatetimeValue()
    {
        $binding  = new DateTimeBinding('date', 'date');
        $person   = new Person();
        $property = Property::create($person, 'date');

        $binding->bind(new JsonDecoder(), ['date' => 'invalid'], $property);

        $this->assertFalse(property_exists($person, 'date'));
    }
}
