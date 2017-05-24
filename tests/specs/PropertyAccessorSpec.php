<?php

namespace tests\specs\Karriere\JsonDecoder;

use Karriere\JsonDecoder\PropertyAccessor;
use PhpSpec\ObjectBehavior;
use PhpSpec\Wrapper\Collaborator;
use Prophecy\Argument;

class PropertyAccessorSpec extends ObjectBehavior
{
    public function let(\ReflectionProperty $reflectionProperty, SampleClass $sampleClass)
    {
        $this->beConstructedWith($reflectionProperty, $sampleClass, false);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(PropertyAccessor::class);
    }

    public function it_should_use_normal_set_for_public_properties(\ReflectionProperty $reflectionProperty, SampleClass $sampleClass)
    {
        /* @var Collaborator $sampleClass */

        $reflectionProperty->getName()->willReturn('property')->shouldBeCalled();
        $reflectionProperty->setAccessible(Argument::any())->shouldNotBeCalled();
        $reflectionProperty->setValue($sampleClass, 'value')->shouldNotBeCalled();

        $this->set('value');
    }

    public function it_should_use_the_reflection_property_for_private_property(\ReflectionProperty $reflectionProperty, SampleClass $sampleClass)
    {
        $this->beConstructedWith($reflectionProperty, $sampleClass, true);

        $reflectionProperty->getName()->willReturn('property')->shouldNotBeCalled();
        $reflectionProperty->setAccessible(true)->shouldBeCalled();
        $reflectionProperty->setAccessible(false)->shouldBeCalled();
        $reflectionProperty->setValue($sampleClass, 'value')->shouldBeCalled();

        $this->set('value');
    }
}

class SampleClass
{
    public $property;
}
