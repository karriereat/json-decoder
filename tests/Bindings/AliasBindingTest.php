<?php

namespace Karriere\JsonDecoder\Tests\Bindings;

use Karriere\JsonDecoder\Bindings\AliasBinding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Property;
use Karriere\JsonDecoder\Tests\Fakes\Person;
use PHPUnit\Framework\TestCase;

class AliasBindingTest extends TestCase
{
    /** @test */
    public function itAliasesAField()
    {
        $binding  = new AliasBinding('firstname', 'first-name');
        $person   = new Person();
        $property = Property::create($person, 'firstname');

        $binding->bind(new JsonDecoder(), ['first-name' => 'John'], $property);

        $this->assertEquals('John', $person->firstname());
    }

    /** @test */
    public function itSkipsANotAvailableField()
    {
        $binding  = new AliasBinding('lastname', 'lastname');
        $person   = new Person();
        $property = Property::create($person, 'firstname');

        $binding->bind(new JsonDecoder(), ['first-name' => 'John'], $property);

        $this->assertNull($person->firstname());
        $this->assertNull($person->lastname());
    }
}
