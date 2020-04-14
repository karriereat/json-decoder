<?php

namespace Karriere\JsonDecoder\Tests\Bindings;

use Karriere\JsonDecoder\Bindings\FieldBinding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Property;
use Karriere\JsonDecoder\Tests\Fakes\Address;
use Karriere\JsonDecoder\Tests\Fakes\Person;
use PHPUnit\Framework\TestCase;

class FieldBindingTest extends TestCase
{
    /** @test */
    public function it_binds_a_field_to_a_class_instance()
    {
        $binding = new FieldBinding('address', 'address', Address::class);
        $person = new Person();
        $property = Property::create($person, 'address');
        $jsonData = json_decode(file_get_contents(__DIR__ . '/../data/personWithAddress.json'), true);

        $binding->bind(new JsonDecoder(), $jsonData, $property);

        $this->assertInstanceOf(Address::class, $person->address());
        $this->assertEquals('Street', $person->address()->street());
        $this->assertEquals('City', $person->address()->city());
    }

    /** @test */
    public function it_ignores_a_not_defined_field()
    {
        $binding = new FieldBinding('address', 'address', Address::class);
        $person = new Person();
        $property = Property::create($person, 'address');

        $binding->bind(new JsonDecoder(), [], $property);

        $this->assertNull($person->address());
    }
}
