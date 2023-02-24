<?php

namespace Karriere\JsonDecoder\Tests\Fakes;

use Karriere\JsonDecoder\Bindings\AliasBinding;
use Karriere\JsonDecoder\Bindings\DateTimeBinding;
use Karriere\JsonDecoder\Bindings\FieldBinding;
use Karriere\JsonDecoder\ClassBindings;
use Karriere\JsonDecoder\Transformer;

class ComplexTransformer implements Transformer
{
    public function register(ClassBindings $classBindings): void
    {
        $classBindings->register(new AliasBinding('firstname', 'first-name'));
        $classBindings->register(new AliasBinding('lastname', 'last-name'));
        $classBindings->register(new FieldBinding('address', 'addr', Address::class));
        $classBindings->register(new FieldBinding('typedAddress', 'typed-addr', Address::class));
        $classBindings->register(new DateTimeBinding('birthday', 'bd'));
    }

    /**
     * @return class-string
     */
    public function transforms(): string
    {
        return Person::class;
    }
}
