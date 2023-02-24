<?php

use Karriere\JsonDecoder\Bindings\DateTimeBinding;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Property;
use Karriere\JsonDecoder\Tests\Fakes\Person;

it('validates successfully', function () {
    expect(new DateTimeBinding(property: 'date', jsonField: 'date'))
        ->validate(['date' => '2020-01-01T12:00:00+00:00'])
        ->toBeTrue();
});

it('fails on validation', function (array $data, bool $required, bool $expected) {
    expect(new DateTimeBinding(property: 'date', jsonField: 'date', isRequired: $required))
        ->validate($data)
        ->toEqual($expected);
})->with([
    'required-and-invalid' => [
        'data' => ['date' => 'invalid'],
        'required' => true,
        'expected' => false,
    ],
    'not-required-and-invalid' => [
        'data' => ['date' => 'invalid'],
        'required' => false,
        'expected' => false,
    ],
    'required-and-not-set' => [
        'data' => [],
        'required' => true,
        'expected' => false,
    ],
    'not-required-and-not-set' => [
        'data' => [],
        'required' => false,
        'expected' => true,
    ],
    'not-required-and-empty' => [
        'data' => ['date' => ''],
        'required' => false,
        'expected' => true,
    ],
]);

it('binds an atom date time', function () {
    $person = new Person();

    (new DateTimeBinding('date', 'date'))
        ->bind(new JsonDecoder(), Property::create($person, 'date'), ['date' => '2020-01-01T12:00:00+00:00']);

    expect($person)
        ->date->toEqual(DateTime::createFromFormat(DateTime::ATOM, '2020-01-01T12:00:00+00:00'));
});

it('ignores different date time values', function (string $value) {
    $person = new Person();

    (new DateTimeBinding('date', 'date'))
        ->bind(new JsonDecoder(), Property::create($person, 'date'), ['date' => $value]);

    expect($person)->not->toHaveProperty('date');
})->with([
    '',
    'invalid',
]);
