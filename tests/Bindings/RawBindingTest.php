<?php

namespace Karriere\JsonDecoder\Tests\Bindings;

use Karriere\JsonDecoder\Bindings\RawBinding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Property;
use Karriere\JsonDecoder\Tests\Fakes\Person;
use PHPUnit\Framework\TestCase;

class RawBindingTest extends TestCase
{
    /** @test */
    public function itSetsARawValue()
    {
        $binding  = new RawBinding('firstname');
        $person   = new Person();
        $property = Property::create($person, 'firstname');

        $binding->bind(new JsonDecoder(), ['firstname' => 'John'], $property);

        $this->assertEquals('John', $person->firstname());
    }

    /** @test */
    public function itIgnoresANotExistingProperty()
    {
        $binding  = new RawBinding('firstname');
        $person   = new Person();
        $property = Property::create($person, 'firstname');

        $binding->bind(new JsonDecoder(), [], $property);

        $this->assertNull($person->firstname());
    }

    /** @test */
    public function itAlwaysValidatesToTrue()
    {
        $binding = new RawBinding('firstname');

        $this->assertTrue($binding->validate([]));
    }
}
