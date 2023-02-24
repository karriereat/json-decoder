<?php

use Karriere\JsonDecoder\Exceptions\InvalidJsonException;
use Karriere\JsonDecoder\Exceptions\NotExistingRootException;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Tests\Fakes\Address;
use Karriere\JsonDecoder\Tests\Fakes\ComplexTransformer;
use Karriere\JsonDecoder\Tests\Fakes\Person;
use Karriere\JsonDecoder\Tests\Fakes\Sample;
use Karriere\JsonDecoder\Tests\Fakes\SimpleTransformer;
use Karriere\JsonDecoder\Transformer;

it('fails with invalid json input', function () {
    (new JsonDecoder())->decode('invalid', Person::class);
})->throws(InvalidJsonException::class);

it('is able to decode json without a transformer', function () {
    expect(getPerson(jsonFile: 'personWithAddress'))
        ->toBeInstanceOf(Person::class)
        ->firstname()->toEqual('John')
        ->lastname()->toEqual('Doe')
        ->address()->toBeArray()
        ->address()->toEqual([
            'street' => 'Street',
            'city' => 'City',
        ])
        ->unionType()->toBeNull()
        ->typedAddress()->toBeNull();
});

it('is able to decode json with different transformers', function (Transformer $transformer, string $jsonFile) {
    $jsonDecoder = new JsonDecoder();
    $jsonDecoder->register($transformer);

    expect(getPerson(jsonFile: $jsonFile, jsonDecoder: $jsonDecoder))
        ->toBeInstanceOf(Person::class)
        ->firstname()->toEqual('John')
        ->lastname()->toEqual('Doe')
        ->address()->toBeInstanceOf(Address::class)
        ->address()->street()->toEqual('Street')
        ->address()->city()->toEqual('City')
        ->typedAddress()->toBeInstanceOf(Address::class)
        ->typedAddress()->street()->toEqual('Street')
        ->typedAddress()->city()->toEqual('City')
        ->unionType()->toBeNull()
        ->birthday()->toEqual(DateTime::createFromFormat(DateTime::ATOM, '2020-01-01T12:00:00+00:00'));
})->with([
    'simple-transformer' => [
        'transformer' => new SimpleTransformer(),
        'jsonFile' => 'personWithAddress',
    ],
    'complex-transformer' => [
        'transformer' => new ComplexTransformer(),
        'jsonFile' => 'personWithMapping',
    ],
]);

it('decodes multiple instances of a type', function () {
    $persons = getMultiplePersons('persons');

    expect($persons)
        ->toBeArray()
        ->toHaveCount(2);

    expect($persons[0])->firstname()->toEqual('John');
    expect($persons[1])->firstname()->toEqual('Jane');
});

it('decodes an object with a root key', function () {
    expect(getPerson(jsonFile: 'personWithRoot', rootKey: 'person'))
        ->firstname()->toEqual('John')
        ->lastname()->toEqual('Doe');
});

it('decodes multiple objects with a root key', function () {
    $persons = getMultiplePersons(jsonFile: 'personsWithRoot', rootKey: 'persons');

    expect($persons)
        ->toBeArray()
        ->toHaveCount(2);

    expect($persons[0])->firstname()->toEqual('John');
    expect($persons[1])->firstname()->toEqual('Jane');
});

it('scans class and generates a transformer', function () {
    $jsonDecoder = new JsonDecoder();
    $jsonDecoder->scanAndRegister(Person::class);

    expect(getPerson(jsonFile: 'personWithAddress', jsonDecoder: $jsonDecoder))
        ->toBeInstanceOf(Person::class)
        ->firstname()->toEqual('John')
        ->lastname()->toEqual('Doe')
        ->address()->toBeInstanceOf(Address::class)
        ->address()->street()->toEqual('Street')
        ->address()->city()->toEqual('City')
        ->typedAddress()->toBeInstanceOf(Address::class)
        ->typedAddress()->street()->toEqual('Street')
        ->typedAddress()->city()->toEqual('City')
        ->unionType()->toBeNull()
        ->birthday()->toEqual(DateTime::createFromFormat(DateTime::ATOM, '2020-01-01T12:00:00+00:00'));
});

it('can auto case to camel', function (string $exampleFile) {
    expect((new JsonDecoder(shouldAutoCase: true))->decode(
        file_get_contents(sprintf(__DIR__ . '/data/%s.json', $exampleFile)),
        Sample::class
    ))
        ->toBeInstanceOf(Sample::class)
        ->publicProperty()->toEqual('value 1')
        ->protectedProperty()->toEqual('value 2')
        ->privateProperty()->toEqual('value 3');
})->with([
    'sampleInSnakeCase',
    'sampleInKebapCase',
]);

it('fails with not existing root key', function () {
    getMultiplePersons(jsonFile: 'personsWithRoot', rootKey: 'not-existing');
})->throws(NotExistingRootException::class);

it('handles empty json strings gracefully', function (string $value, bool $registerTransformer = false) {
    $jsonDecoder = new JsonDecoder();

    if ($registerTransformer) {
        $jsonDecoder->register(new SimpleTransformer());
    }

    expect($jsonDecoder)->decode($value, Person::class)->toBeNull();
})->with([
    ['null'],
    ['{}'],
    ['{}', true],
]);

function getPerson(string $jsonFile, ?JsonDecoder $jsonDecoder = null, ?string $rootKey = null): Person
{
    return ($jsonDecoder ?? new JsonDecoder())
        ->decode(file_get_contents(sprintf(__DIR__ . '/data/%s.json', $jsonFile)), Person::class, $rootKey);
}

function getMultiplePersons(string $jsonFile, ?JsonDecoder $jsonDecoder = null, ?string $rootKey = null): array
{
    return ($jsonDecoder ?? new JsonDecoder())
        ->decodeMultiple(file_get_contents(sprintf(__DIR__ . '/data/%s.json', $jsonFile)), Person::class, $rootKey);
}
