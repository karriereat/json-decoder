<?php

namespace Karriere\JsonDecoder\Tests\Fakes;

use DateTime;

class Person
{
    /**
     * @var string
     */
    private $firstname;

    /**
     * @var string
     */
    private $lastname;

    /**
     * @var Address
     */
    private $address;

    /**
     * @var DateTime
     */
    private $birthday;

    public function firstname()
    {
        return $this->firstname;
    }

    public function lastname()
    {
        return $this->lastname;
    }

    public function address()
    {
        return $this->address;
    }

    public function birthday()
    {
        return $this->birthday;
    }
}
