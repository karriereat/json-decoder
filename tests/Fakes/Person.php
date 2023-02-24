<?php

namespace Karriere\JsonDecoder\Tests\Fakes;

use AllowDynamicProperties;
use DateTime;

#[AllowDynamicProperties]
class Person
{
    private ?string $firstname = null;

    /**
     * @var string
     */
    private $lastname;

    /**
     * @var Address
     */
    private $address;

    private ?Address $typedAddress = null;

    private Address|string|null $unionType = null;

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

    public function typedAddress(): ?Address
    {
        return $this->typedAddress;
    }

    public function birthday()
    {
        return $this->birthday;
    }

    public function unionType(): Address|string|null
    {
        return $this->unionType;
    }
}
