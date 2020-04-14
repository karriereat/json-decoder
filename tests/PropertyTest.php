<?php

namespace Karriere\JsonDecoder\Tests;

use Karriere\JsonDecoder\Property;
use Karriere\JsonDecoder\Tests\Fakes\Sample;
use PHPUnit\Framework\TestCase;

class PropertyTest extends TestCase
{
    /** @test */
    public function it_is_able_to_set_public_property()
    {
        $sample = new Sample();
        $property = Property::create($sample, 'publicProperty');

        $property->set('value');

        $this->assertEquals('value', $sample->publicProperty);
    }

    /** @test */
    public function it_is_able_to_set_protected_property()
    {
        $sample = new Sample();
        $property = Property::create($sample, 'protectedProperty');

        $property->set('value');

        $this->assertEquals('value', $sample->protectedProperty());
    }

    /** @test */
    public function it_is_able_to_set_private_property()
    {
        $sample = new Sample();
        $property = Property::create($sample, 'privateProperty');

        $property->set('value');

        $this->assertEquals('value', $sample->privateProperty());
    }

    /** @test */
    public function it_is_able_to_set_a_new_property()
    {
        $sample = new Sample();
        $property = Property::create($sample, 'newProperty');

        $property->set('value');

        $this->assertEquals('value', $sample->newProperty);
    }
}
