<?php

namespace Karriere\JsonDecoder\Tests\Bindings;

use Karriere\JsonDecoder\Bindings\ArrayBinding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Property;
use Karriere\JsonDecoder\Tests\Fakes\Address;
use Karriere\JsonDecoder\Tests\Fakes\Person;
use PHPUnit\Framework\TestCase;

class ArrayBindingTest extends TestCase
{
    /** @test */
    public function it_binds_an_array()
    {
        $binding  = new ArrayBinding('address', 'addresses', Address::class);
        $person   = new Person();
        $property = Property::create($person, 'address');

        $jsonData = [
            'addresses' => [
                [
                    'street' => 'Street 1',
                    'city'   => 'City 1',
                ],
                [
                    'street' => 'Street 2',
                    'city'   => 'City 2',
                ],
            ],
        ];

        $binding->bind(new JsonDecoder(), $jsonData, $property);

        $this->assertIsArray($person->address());
        $this->assertCount(2, $person->address());
        $this->assertEquals('Street 1', $person->address()[0]->street());
        $this->assertEquals('City 1', $person->address()[0]->city());
        $this->assertEquals('Street 2', $person->address()[1]->street());
        $this->assertEquals('City 2', $person->address()[1]->city());
    }

    /** @test */
    public function it_skips_a_not_available_field()
    {
        $binding  = new ArrayBinding('address', 'addresses', Address::class);
        $person   = new Person();
        $property = Property::create($person, 'address');

        $binding->bind(new JsonDecoder(), [], $property);

        $this->assertNull($person->address());
    }
}
