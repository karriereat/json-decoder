<a href="https://www.karriere.at/" target="_blank"><img width="200" src="http://www.karriere.at/images/layout/katlogo.svg"></a>
<span>&nbsp;&nbsp;&nbsp;</span>

# JsonDecoder for PHP

This package contains a JsonDecoder implementation that allows you to convert your json data into real php class objects.

## Installation
You can install the package via composer
```
composer require karriere/json-decoder
```

## Usage
By default all not defined fields of the input json will be stored in a class property with the same name.

### A simple example
Assume you have a class `Person` that looks like this:
```php
class Person {
    public $id;
    public $name;
}
```

The following code will transform the given json data into an instance of `Person`.

```php
$jsonDecoder = new JsonDecoder();
$jsonData = '{"id": 1, "name": "John Doe"}';
$arrayData = json_decode($jsonData, true);

$person = $jsonDecoder->decode($arrayData, Person::class);

```

## License

Apache License 2.0 Please see [LICENSE](LICENSE) for more information.