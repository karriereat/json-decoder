<?php

namespace Karriere\JsonDecoder\Tests\Fakes;

use AllowDynamicProperties;

#[AllowDynamicProperties]
class Sample
{
    public $publicProperty;

    protected $protectedProperty;

    private $privateProperty;

    private ?string $nullableString = null;

    private int $intProperty = 0;

    private int|string $unionTypeProperty = 0;

    private int|string|null $nullableUnionTypeProperty = 0;

    public function publicProperty()
    {
        return $this->publicProperty;
    }

    public function protectedProperty()
    {
        return $this->protectedProperty;
    }

    public function privateProperty()
    {
        return $this->privateProperty;
    }

    public function nullableString(): ?string
    {
        return $this->nullableString;
    }

    public function intProperty(): int
    {
        return $this->intProperty;
    }

    public function unionTypeProperty(): int|string
    {
        return $this->unionTypeProperty;
    }

    public function nullableUnionTypeProperty(): int|string|null
    {
        return $this->nullableUnionTypeProperty;
    }
}
