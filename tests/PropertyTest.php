<?php

namespace Karriere\JsonDecoder\Tests;

use Karriere\JsonDecoder\Property;
use Karriere\JsonDecoder\Tests\Fakes\Sample;
use PHPUnit\Framework\TestCase;

class PropertyTest extends TestCase
{
    /** @test */
    public function itIsAbleToSetPublicProperty()
    {
        $sample   = new Sample();
        $property = Property::create($sample, 'publicProperty');

        $property->set('value');

        $this->assertEquals('value', $sample->publicProperty);
    }

    /** @test */
    public function itIsAbleToSetProtectedProperty()
    {
        $sample   = new Sample();
        $property = Property::create($sample, 'protectedProperty');

        $property->set('value');

        $this->assertEquals('value', $sample->protectedProperty());
    }

    /** @test */
    public function itIsAbleToSetPrivateProperty()
    {
        $sample   = new Sample();
        $property = Property::create($sample, 'privateProperty');

        $property->set('value');

        $this->assertEquals('value', $sample->privateProperty());
    }

    /** @test */
    public function itIsAbleToSetANewProperty()
    {
        $sample   = new Sample();
        $property = Property::create($sample, 'newProperty');

        $property->set('value');

        $this->assertEquals('value', $sample->newProperty);
    }
}
