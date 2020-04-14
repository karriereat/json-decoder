<?php

namespace Karriere\JsonDecoder\Tests\Fakes;

class Sample
{
    public $publicProperty;

    protected $protectedProperty;

    private $privateProperty;

    public function protectedProperty()
    {
        return $this->protectedProperty;
    }

    public function privateProperty()
    {
        return $this->privateProperty;
    }
}
