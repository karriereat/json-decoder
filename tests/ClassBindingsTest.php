<?php

use Karriere\JsonDecoder\Bindings\CallbackBinding;
use Karriere\JsonDecoder\Bindings\FieldBinding;
use Karriere\JsonDecoder\ClassBindings;
use Karriere\JsonDecoder\Exceptions\JsonValueException;
use Karriere\JsonDecoder\JsonDecoder;
use Karriere\JsonDecoder\Tests\Fakes\Person;
use Karriere\JsonDecoder\Tests\Fakes\ValidationFailBinding;

beforeEach(function () {
    $this->classBindings = new ClassBindings(new JsonDecoder());
});

it('registers a field binding', function () {
    expect($this->classBindings)->hasBinding('field')->toBeFalse();

    $this->classBindings->register(new FieldBinding('field', 'field', Person::class));

    expect($this->classBindings)->hasBinding('field')->toBeTrue();
});

it('registers a callback binding', function () {
    expect($this->classBindings)->hasBinding('field')->toBeFalse();
    expect($this->classBindings)->hasCallbackBinding('field')->toBeFalse();

    $this->classBindings->register(new CallbackBinding('field', function (): void {
    }));

    expect($this->classBindings)->hasBinding('field')->toBeFalse();
    expect($this->classBindings)->hasCallbackBinding('field')->toBeTrue();
});

it('throws exception if binding validation fails', function () {
    $this->classBindings->register(new ValidationFailBinding('firstname', 'firstname'));

    $this->classBindings->decode(['firstname' => 'John'], new Person());
})->throws(JsonValueException::class);

it('executes callback bindings when property name is contained in json fields', function () {
    $this->classBindings->register(
        new CallbackBinding('firstname', fn (array $data): string => $data['firstname'] . ' Doe')
    );

    expect($this->classBindings->decode(['firstname' => 'John'], new Person()))
        ->firstname()->toEqual('John Doe');
});

it('executes callback bindings when property name is not contained in json fields', function () {
    $this->classBindings->register(
        new CallbackBinding('foo', fn (array $data): string => 'bar')
    );

    expect($this->classBindings->decode(['firstname' => 'John'], new Person()))
        ->foo->toEqual('bar');
});
