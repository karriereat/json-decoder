<?php

namespace Karriere\JsonDecoder\Tests;

use Karriere\JsonDecoder\Binding;
use Karriere\JsonDecoder\Bindings\FieldBinding;
use Karriere\JsonDecoder\ClassBindings;
use Karriere\JsonDecoder\Exceptions\InvalidBindingException;
use Karriere\JsonDecoder\Exceptions\JsonValueException;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Tests\Fakes\Person;
use PHPUnit\Framework\TestCase;

class ClassBindingsTest extends TestCase
{
    /** @test */
    public function it_registers_a_binding()
    {
        $classBindings = new ClassBindings(new JsonDecoder());

        $this->assertFalse($classBindings->hasBinding('field'));

        $classBindings->register(new FieldBinding('field', 'field', Person::class));

        $this->assertTrue($classBindings->hasBinding('field'));
    }

    /** @test */
    public function it_fails_to_register_a_not_compatible_binding_class()
    {
        $classBindings = new ClassBindings(new JsonDecoder());

        $this->expectException(InvalidBindingException::class);

        $classBindings->register(new Person());
    }

    /** @test */
    public function it_throws_an_exception_if_binding_validation_fails()
    {
        $classBindings = new ClassBindings(new JsonDecoder());

        $classBindings->register(new class('firstname', 'firstname', 'type') extends Binding {
            public function validate($jsonData): bool
            {
                return false;
            }

            public function bind($jsonDecoder, $jsonData, $property)
            {
            }
        });

        $this->expectException(JsonValueException::class);

        $classBindings->decode(['firstname' => 'John'], new Person());
    }
}
