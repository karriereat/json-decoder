<a href="https://www.karriere.at/" target="_blank"><img width="200" src="http://www.karriere.at/images/layout/katlogo.svg"></a>
<span>&nbsp;&nbsp;&nbsp;</span>
[![Build Status](https://travis-ci.org/karriereat/json-decoder.svg?branch=master)](https://travis-ci.org/karriereat/json-decoder)
[![codecov](https://codecov.io/gh/karriereat/json-decoder/branch/master/graph/badge.svg)](https://codecov.io/gh/karriereat/json-decoder)
[![StyleCI](https://styleci.io/repos/89163851/shield?branch=master)](https://styleci.io/repos/89163851)

# JsonDecoder for PHP

This package contains a JsonDecoder implementation that allows you to convert your JSON data into php class objects other than `stdclass`.

## Installation
You can install the package via composer
```
composer require karriere/json-decoder
```

## Usage
By default all public properties of the class will be inspected. For all properties that have a JSON key with the same name the according value will be set.

### A simple example
Assume you have a class `Person` that looks like this:
```php
class Person
{
    public $id;
    public $name;
}
```

The following code will transform the given JSON data into an instance of `Person`.

```php
$jsonDecoder = new JsonDecoder();
$jsonData = '{"id": 1, "name": "John Doe"}';

$person = $jsonDecoder->decode($jsonData, Person::class);
```

### Defining a Transformer
Let's extend the previous example with a property called address. This address field should contain an instance of `Address`.
```php
class Person
{
    public $id;
    public $name;
    public $address;
}
```

To be able to transform the address data into an `Address` class object you need to define a transformer for `Person`:

The transformer interface defines two methods:

* register: here you register your field, array, alias and callback bindings
* transforms: gives you the full qualified class name e.g.: Your\Namespace\Class
```php
class PersonTransformer implements Transformer
{
    public function register(ClassBindings $classBindings)
    {
        $classBindings->register(new FieldBinding('address', 'address', Address::class);
    }

    public function transforms()
    {
        return Person::class;
    }
}
```

After registering the transformer the `JsonDecoder` will use the defined transformer:
```php
$jsonDecoder = new JsonDecoder();
$jsonDecoder->register(new PersonTransformer());

$jsonData = '{"id": 1, "name": "John Doe"}';

$person = $jsonDecoder->decode($jsonData, Person::class);
```

### Handling private and protected properties
The `JsonDecoder` class accepts two boolean constructor parameters to enable the handling of private and protected properties.

To do so a so called `PropertyAccessor` will be installed and on property set the proxy will set the property to accessible, set the according value and then will set the property to not accessible again.

### Transforming an array of elements
If your JSON contains an array of elements at the root level you can use the `decodeMultiple` method to transform the JSON data into an array of class type objects.

```php
$jsonDecoder = new JsonDecoder();

$jsonData = '[{"id": 1, "name": "John Doe"}, {"id": 2, "name": "Jane Doe"}]';

$personArray = $jsonDecoder->decodeMultiple($jsonData, Person::class);
```

## Documentation

### Transformer Bindings
The following `Binding` implementations are available

* [FieldBinding](#fieldbinding)
* [ArrayBinding](#arraybinding)
* [AliasBinding](#aliasbinding)
* [CallbackBinding](#callbackbinding)

#### FieldBinding
Defines a JSON field to property binding for the given type.

**Signature:**
```php
new FieldBinding($property, $jsonField, $type);
```
This defines a field mapping for the property `$property` to a class instance of type `$type` with data in `$jsonField`.

#### ArrayBinding
Defines a array field binding for the given type.

**Signature:**
```php
new ArrayBinding($property, $jsonField, $type);
```
This defines a field mapping for the property `$property` to an array of class instance of type `$type` with data in `$jsonField`.

#### AliasBinding
Defines a JSON field to property binding.

**Signature:**
```php
new AliasBinding($property, $jsonField);
```

#### CallbackBinding
Defines a property binding that gets the callback result set as its value.

**Signature:**
```php
new CallbackBinding($property, $callback);
```

## License

Apache License 2.0 Please see [LICENSE](LICENSE) for more information.
