<?php

namespace Karriere\JsonDecoder\Tests\Fakes;

class Address
{
    private $street;

    private $city;

    public function street()
    {
        return $this->street;
    }

    public function city()
    {
        return $this->city;
    }
}
