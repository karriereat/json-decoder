<?php

namespace Karriere\JsonDecoder\Tests\Bindings;

use Karriere\JsonDecoder\Bindings\CallbackBinding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Property;
use Karriere\JsonDecoder\Tests\Fakes\Person;
use PHPUnit\Framework\TestCase;

class CallbackBindingTest extends TestCase
{
    /** @test */
    public function it_binds_with_a_callback()
    {
        $binding = new CallbackBinding('firstname', function () {
            return 'Jane';
        });
        $person   = new Person();
        $property = Property::create($person, 'firstname');

        $binding->bind(new JsonDecoder(), [], $property);

        $this->assertEquals('Jane', $person->firstname());
    }

    /** @test */
    public function it_always_validates_to_true()
    {
        $binding = new CallbackBinding('firstname', function () {
            return 'Jane';
        });

        $this->assertTrue($binding->validate([]));
    }
}
