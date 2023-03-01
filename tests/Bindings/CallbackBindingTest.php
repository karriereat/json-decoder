<?php

use Karriere\JsonDecoder\Bindings\CallbackBinding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Property;
use Karriere\JsonDecoder\Tests\Fakes\Person;

it('binds with a callback', function () {
    $person = new Person();

    (new CallbackBinding('firstname', fn () => 'Jane'))
        ->bind(new JsonDecoder(), Property::create($person, 'firstname'));

    expect($person)->firstname()->toEqual('Jane');
});

it('always validates to true', function () {
    expect(new CallbackBinding('firstname', fn () => 'Jane'))
        ->validate([])->toBeTrue();
});
