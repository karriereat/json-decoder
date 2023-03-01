<?php

use Karriere\JsonDecoder\Bindings\FieldBinding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Property;
use Karriere\JsonDecoder\Tests\Fakes\Address;
use Karriere\JsonDecoder\Tests\Fakes\Person;

beforeEach(function () {
    $this->binding = new FieldBinding('address', 'address', Address::class);
    $this->person = new Person();
    $this->property = Property::create($this->person, 'address');
});

it('binds a field to a class instance', function () {
    $this->binding->bind(
        new JsonDecoder(),
        $this->property,
        json_decode(file_get_contents(__DIR__ . '/../data/personWithAddress.json'), true),
    );

    expect($this->person->address())
        ->toBeInstanceOf(Address::class)
        ->street()->toEqual('Street')
        ->city()->toEqual('City');
});

it('ignores a not defined field', function () {
    $this->binding->bind(new JsonDecoder(), $this->property);

    expect($this->person)->address()->toBeNull();
});
