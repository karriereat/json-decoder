<?php

use Karriere\JsonDecoder\Bindings\ArrayBinding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Property;
use Karriere\JsonDecoder\Tests\Fakes\Address;
use Karriere\JsonDecoder\Tests\Fakes\Person;

beforeEach(function () {
    $this->binding = new ArrayBinding('address', 'addresses', Address::class);
    $this->person = new Person();
    $this->property = Property::create($this->person, 'address');
});

it('binds an array', function () {
    $this->binding->bind(
        new JsonDecoder(),
        $this->property,
        [
            'addresses' => [
                [
                    'street' => 'Street 1',
                    'city' => 'City 1',
                ],
                [
                    'street' => 'Street 2',
                    'city' => 'City 2',
                ],
            ],
        ],
    );

    expect($this->person)
        ->address()->toBeArray()
        ->address()->toHaveCount(2);

    expect($this->person->address()[0])
        ->street()->toEqual('Street 1')
        ->city()->toEqual('City 1');

    expect($this->person->address()[1])
        ->street()->toEqual('Street 2')
        ->city()->toEqual('City 2');
});

it('binds an array and preserves keys', function () {
    $this->binding->bind(
        new JsonDecoder(),
        $this->property,
        [
            'addresses' => [
                'address key #1' => [
                    'street' => 'Street 1',
                    'city' => 'City 1',
                ],
                'address key #2' => [
                    'street' => 'Street 2',
                    'city' => 'City 2',
                ],
            ],
        ],
    );

    expect($this->person)
        ->address()->toBeArray()
        ->address()->toHaveCount(2);

    expect($this->person->address()['address key #1'])
        ->street()->toEqual('Street 1')
        ->city()->toEqual('City 1');

    expect($this->person->address()['address key #2'])
        ->street()->toEqual('Street 2')
        ->city()->toEqual('City 2');
});

it('skips a not available field', function () {
    $this->binding->bind(new JsonDecoder(), $this->property);

    expect($this->person)->address()->toBeNull();
});
