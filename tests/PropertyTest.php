<?php

use Karriere\JsonDecoder\Property;
use Karriere\JsonDecoder\Tests\Fakes\NoDynamicPropertyAllowed;
use Karriere\JsonDecoder\Tests\Fakes\Sample;

it('is able to set all sorts of untyped properties', function (string $propertyName, bool $methodAccess = true) {
    $sample = new Sample();
    (Property::create($sample, $propertyName))->set('value');

    if ($methodAccess) {
        expect($sample->{$propertyName}())->toEqual('value');
    } else {
        expect($sample->{$propertyName})->toEqual('value');
    }
})->with([
    'public' => [
        'name' => 'publicProperty',
    ],
    'protected' => [
        'name' => 'protectedProperty',
    ],
    'private' => [
        'name' => 'privateProperty',
    ],
    'new' => [
        'name' => 'newProperty',
        'methodAccess' => false,
    ],
]);

it('does not set dynamic property if they are not allowed', function () {
    $object = new NoDynamicPropertyAllowed();
    (Property::create($object, 'dynamicProperty'))->set('value');

    if (version_compare(PHP_VERSION, '8.2.0', '<')) {
        expect($object)->toHaveProperty('dynamicProperty');
    } else {
        expect($object)->not->toHaveProperty('dynamicProperty');
    }
});

it('sets value if property has type and it matches the values type', function () {
    $sample = new Sample();
    (Property::create($sample, 'intProperty'))->set(10);

    expect($sample)->intProperty()->toEqual(10);
});

it('sets value to null if null is passed and property has nullable type', function () {
    $sample = new Sample();
    (Property::create($sample, 'nullableString'))->set(null);

    expect($sample)->nullableString()->toBeNull();
});

it('does not set value if types do not match', function () {
    $sample = new Sample();
    (Property::create($sample, 'nullableString'))->set(10);

    expect($sample)->nullableString()->not->toEqual(10);
});

it('does set value if property has union type and value matches one of those types', function () {
    $sample = new Sample();
    (Property::create($sample, 'unionTypeProperty'))->set(20);

    expect($sample)->unionTypeProperty()->toEqual(20);
});

it('does not set value if union types do not match', function () {
    $sample = new Sample();
    (Property::create($sample, 'unionTypeProperty'))->set([]);

    expect($sample)->unionTypeProperty()->not->toEqual([]);
});

it('sets value to null if null is passed and property has nullable union types', function () {
    $sample = new Sample();
    (Property::create($sample, 'nullableUnionTypeProperty'))->set(null);

    expect($sample)->nullableUnionTypeProperty()->toBeNull();
});
