<?php

namespace Karriere\JsonDecoder\Tests;

use Karriere\JsonDecoder\Binding;
use Karriere\JsonDecoder\Bindings\CallbackBinding;
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
    public function itRegistersABinding()
    {
        $classBindings = new ClassBindings(new JsonDecoder());

        $this->assertFalse($classBindings->hasBinding('field'));

        $classBindings->register(new FieldBinding('field', 'field', Person::class));

        $this->assertTrue($classBindings->hasBinding('field'));
    }

    /** @test */
    public function itRegistersACallbackBinding()
    {
        $classBindings = new ClassBindings(new JsonDecoder());

        $this->assertFalse($classBindings->hasBinding('field'));
        $this->assertFalse($classBindings->hasCallbackBinding('field'));

        $classBindings->register(new CallbackBinding('field', function () {}));

        $this->assertFalse($classBindings->hasBinding('field'));
        $this->assertTrue($classBindings->hasCallbackBinding('field'));
    }

    /** @test */
    public function itFailsToRegisterANotCompatibleBindingClass()
    {
        $classBindings = new ClassBindings(new JsonDecoder());

        $this->expectException(InvalidBindingException::class);

        $classBindings->register(new Person());
    }

    /** @test */
    public function itThrowsAnExceptionIfBindingValidationFails()
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

    /** @test */
    public function itExecutesCallbackBindingsWhenPropertyNameIsContainedInJsonFields()
    {
        $classBindings = new ClassBindings(new JsonDecoder());
        $classBindings->register(new CallbackBinding('firstname', function ($data) {
            return $data['firstname'] . ' Doe';
        }));

        $person = $classBindings->decode(['firstname' => 'John'], new Person());

        $this->assertEquals('John Doe', $person->firstname());
    }

    /** @test */
    public function itExecutesCallbackBindingsWhenPropertyNameIsNotContainedInJsonFields()
    {
        $classBindings = new ClassBindings(new JsonDecoder());
        $classBindings->register(new CallbackBinding('somePropertyName', function () {
            return 'yes';
        }));

        $person = $classBindings->decode(['firstname' => 'John'], new Person());

        $this->assertEquals('yes', $person->somePropertyName);
    }
}
