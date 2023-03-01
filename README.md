<a href="https://www.karriere.at/" target="_blank"><img width="200" src="https://raw.githubusercontent.com/karriereat/.github/main/profile/logo.svg"></a>
<span>&nbsp;&nbsp;&nbsp;</span>
![](https://github.com/karriereat/json-decoder/workflows/CI/badge.svg)
[![Packagist Downloads](https://img.shields.io/packagist/dt/karriere/json-decoder.svg?style=flat-square)](https://packagist.org/packages/karriere/json-decoder)

# JsonDecoder for PHP

This package contains a JsonDecoder implementation that allows you to convert your JSON data into php class objects other than `stdclass`.

## Installation

You can install the package via composer

```
composer require karriere/json-decoder
```

## Usage

By default the Decoder will iterate over all JSON fields defined and will try to set this values on the given class type instance. This change in behavior allows the use of `json-decoder` on classes that use the **magic** `__get` and `__set` functions like Laravel's Eloquent models.

If a property equally named like the JSON field is found or a explicit `Binding` is defined for the JSON field it will be decoded into the defined place. Otherwise the property will just be created and assigned (you need the `#[AllowDynamicProperties]` attribute if you are on PHP 8.2.).

The `JsonDecoder` class can receive one parameter called `shouldAutoCase`. If set to true it will try to find the camel-case version from either snake-case or kebap-case automatically if no other binding was registered for the field and it will use an `AliasBinding` if one of the variants can be found.

### A simple example

Assume you have a class `Person` that looks like this:

```php
#[AllowDynamicProperties]
class Person
{
    public int $id;
    public string $name;
    public ?string $lastname = '';
}
```

The following code will transform the given JSON data into an instance of `Person`.

```php
$jsonDecoder = new JsonDecoder();
$jsonData = '{"id": 1, "name": "John Doe", "lastname": null, "dynamicProperty": "foo"}';

$person = $jsonDecoder->decode($jsonData, Person::class);
```

Please be aware that since PHP 8.2. dynamic properties are deprecated. So if you still wish to have the ability to make
use of those dynamic properties you have to add the PHP attribute `AllowDynamicProperties` to your class.
If you are using PHP 8.2. (and greater) and don't use the `AllowDynamicProperties` attribute all dynamic properties will
be ignored.

### More complex use case

Let's extend the previous example with a property called address. This address field should contain an instance of `Address`.
As of version 4 you can use the introduced method `scanAndRegister` to automatically generate the transformer based on class annotations.
Since version 5 you can also make use of the property type instead of a class annotation.

```php
class Person
{
    public int $id;
    public string $name;

    /**
     * @var Address
     */
    public $address;
    
    public ?Address $typedAddress = null;
}
```

For this class definition we can decode JSON data as follows:

```php
$jsonDecoder = new JsonDecoder();
$jsonDecoder->scanAndRegister(Person::class);

$jsonData = '{"id": 1, "name": "John Doe", "address": {"street": "Samplestreet", "city": "Samplecity"}, , "typedAddress": {"street": "Samplestreet", "city": "Samplecity"}}';

$person = $jsonDecoder->decode($jsonData, Person::class);
```

### Defining a Transformer

If you don't use annotations or need a more flexible `Transformer` you can also create a custom transformer. Let's look at the previous example without annotation.

```php
class Person
{
    public int $id;
    public string $name;
    public mixed $address;
}
```

To be able to transform the address data into an `Address` class object you need to define a transformer for `Person`:

The transformer interface defines two methods:

-   register: here you register your field, array, alias and callback bindings
-   transforms: gives you the full qualified class name e.g.: Your\Namespace\Class

```php
class PersonTransformer implements Transformer
{
    public function register(ClassBindings $classBindings)
    {
        $classBindings->register(new FieldBinding('address', 'address', Address::class));
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

As of version 4 the `JsonDecoder` class will handle `private` and `protected` properties out of the box.

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

-   [FieldBinding](#fieldbinding)
-   [ArrayBinding](#arraybinding)
-   [AliasBinding](#aliasbinding)
-   [DateTimeBinding](#datetimebinding)
-   [CallbackBinding](#callbackbinding)

#### FieldBinding

Defines a JSON field to property binding for the given type.

**Signature:**

```php
new FieldBinding(string $property, ?string $jsonField = null, ?string $type = null, bool $isRequired = false);
```

This defines a field mapping for the property `$property` to a class instance of type `$type` with data in `$jsonField`.

#### ArrayBinding

Defines an array field binding for the given type.

**Signature:**

```php
new ArrayBinding(string $property, ?string $jsonField = null, ?string $type = null, bool $isRequired = false);
```

This defines a field mapping for the property `$property` to an array of class instance of type `$type` with data in `$jsonField`.

#### AliasBinding

Defines a JSON field to property binding.

**Signature:**

```php
new AliasBinding(string $property, ?string $jsonField = null, bool $isRequired = false);
```

#### DateTimeBinding

Defines a JSON field to property binding and converts the given string to a `DateTime` instance.

**Signature:**

```php
new DateTimeBinding(string $property, ?string $jsonField = null, bool $isRequired = false, $dateTimeFormat = DateTime::ATOM);
```

#### CallbackBinding

Defines a property binding that gets the callback result set as its value.

**Signature:**

```php
new CallbackBinding(string $property, private Closure $callback);
```

## License

Apache License 2.0 Please see [LICENSE](LICENSE) for more information.
