<?php

namespace Karriere\JsonDecoder\Tests\Fakes;

use Karriere\JsonDecoder\Bindings\DateTimeBinding;
use Karriere\JsonDecoder\Bindings\FieldBinding;
use Karriere\JsonDecoder\ClassBindings;
use Karriere\JsonDecoder\Transformer;

class SimpleTransformer implements Transformer
{
    public function register(ClassBindings $classBindings): void
    {
        $classBindings->register(new FieldBinding('address', 'address', Address::class));
        $classBindings->register(new FieldBinding('typedAddress', 'typedAddress', Address::class));
        $classBindings->register(new DateTimeBinding('birthday', 'birthday'));
    }

    /**
     * @return class-string
     */
    public function transforms(): string
    {
        return Person::class;
    }
}
