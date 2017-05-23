<?php

namespace tests\specs\Karriere\JsonDecoder;

use Karriere\JsonDecoder\Binding;
use Karriere\JsonDecoder\ClassBindings;
use Karriere\JsonDecoder\Exceptions\InvalidBindingException;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\PropertyAccessor;
use PhpSpec\ObjectBehavior;

class ClassBindingsSpec extends ObjectBehavior
{
    function let(JsonDecoder $jsonDecoder)
    {
        $this->beConstructedWith($jsonDecoder);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ClassBindings::class);
    }

    function it_should_register_a_binding(Binding $binding)
    {
        $this->register($binding);
    }

    function it_should_throw_an_exception_if_no_binding_is_passed_to_register()
    {
        $this->shouldThrow(InvalidBindingException::class)->duringRegister("foo");
    }

    function it_should_set_raw_data()
    {
        $instance = new Sample();

        $response = $this->decode(['publicField' => 'data'], $instance);

        $response->publicField->shouldBe('data');
    }

    function it_should_only_decode_public_data()
    {
        $instance = new Sample();

        $response = $this->decode([
            'publicField' => 'data',
            'protectedField' => 'protected data',
            'privateField' => 'private data',
        ], $instance);

        $response->publicData()->shouldReturn('data');
        $response->protectedData()->shouldBeNull();
        $response->privateData()->shouldBeNull();
    }

    function it_should_decode_protected_data(JsonDecoder $jsonDecoder)
    {
        $jsonDecoder->decodesProtectedProperties()->willReturn(true)->shouldBeCalled();
        $jsonDecoder->decodesPrivateProperties()->willReturn(false)->shouldBeCalled();

        $instance = new Sample();

        $response = $this->decode([
            'publicField' => 'data',
            'protectedField' => 'protected data',
            'privateField' => 'private data',
        ], $instance);

        $response->publicData()->shouldReturn('data');
        $response->protectedData()->shouldReturn('protected data');
        $response->privateData()->shouldBeNull();
    }

    function it_should_decode_private_data(JsonDecoder $jsonDecoder)
    {
        $jsonDecoder->decodesProtectedProperties()->willReturn(false)->shouldBeCalled();
        $jsonDecoder->decodesPrivateProperties()->willReturn(true)->shouldBeCalled();

        $instance = new Sample();

        $response = $this->decode([
            'publicField' => 'data',
            'protectedField' => 'protected data',
            'privateField' => 'private data',
        ], $instance);

        $response->publicData()->shouldReturn('data');
        $response->protectedData()->shouldBeNull();
        $response->privateData()->shouldReturn('private data');
    }

    function it_should_call_binding_for_property(JsonDecoder $jsonDecoder, Binding $binding)
    {
        $sample = new Sample();
        $accessor = new PropertyAccessor(new \ReflectionProperty($sample, 'publicField'), $sample);

        $binding->property()->willReturn('publicField')->shouldBeCalled();
        $binding->bind($jsonDecoder, [
            'publicField' => 'data',
            'protectedField' => 'protected data',
            'privateField' => 'private data',
        ], $accessor)->shouldBeCalled();

        $this->register($binding);

        $this->decode([
            'publicField' => 'data',
            'protectedField' => 'protected data',
            'privateField' => 'private data',
        ], $sample);
    }
}

class Sample {
    public $publicField;
    protected $protectedField;
    private $privateField;

    public function publicData()
    {
        return $this->publicField;
    }

    public function protectedData()
    {
        return $this->protectedField;
    }

    public function privateData()
    {
        return $this->privateField;
    }
}
