<?php

use Karriere\JsonDecoder\Bindings\RawBinding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Property;
use Karriere\JsonDecoder\Tests\Fakes\Person;

beforeEach(function () {
    $this->binding = new RawBinding('firstname');
    $this->person = new Person();
    $this->property = Property::create($this->person, 'firstname');
});

it('sets a raw binding', function () {
    $this->binding->bind(new JsonDecoder(), $this->property, ['firstname' => 'John']);

    expect($this->person)->firstname()->toEqual('John');
});

it('ignores a not existing property', function () {
    $this->binding->bind(new JsonDecoder(), $this->property);

    expect($this->person)->firstname()->toBeNull();
});

it('always validates to true', function () {
    expect($this->binding)->validate([])->toBeTrue();
});
