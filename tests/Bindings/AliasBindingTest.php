<?php

use Karriere\JsonDecoder\Bindings\AliasBinding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Property;
use Karriere\JsonDecoder\Tests\Fakes\Person;

beforeEach(function () {
    $this->person = new Person();
    $this->property = Property::create($this->person, 'firstname');
});

it('aliases a field', function () {
    (new AliasBinding('firstname', 'first-name'))
        ->bind(new JsonDecoder(), $this->property, ['first-name' => 'John']);

    expect($this->person)->firstname()->toEqual('John');
});

it('skips a not available field', function () {
    (new AliasBinding('lastname', 'lastname'))
        ->bind(new JsonDecoder(), $this->property, ['first-name' => 'John']);

    expect($this->person)
        ->firstname()->toBeNull()
        ->lastname()->toBeNull();
});
