# Upgrade Guide

## v4 to v5
The most notable change in this major release is definitely the signature change of all "Binding" classes as the order
of the arguments changed (the `$property` comes now before the `$jsonData`). All other signature changes are due to added
type hints and return types, so these changes should most likely not be breaking.

If you extend the `JsonDecoder` class please be aware that the order of arguments in `transform` and `transformRaw` has
also changed (the `$instance` property comes now before the `$jsonArrayData`).

If you upgraded to PHP 8.2.:
As dynamic properties have been deprecated with this PHP version they no longer work out of the box. The setting of
dynamic properties will fail silently. If you rely on dynamic properties make sure that you add the `#[AllowDynamicProperties]`
PHP attribute to your class (see the example in the [README.md](README.md).

### Constructor signature changes

#### Binding.php
```php
// Before
public function __construct($property, $jsonField, $type, $isRequired = false) {}

// After
public function __construct(
    protected string $property,
    protected ?string $jsonField = null,
    protected ?string $type = null,
    protected bool $isRequired = false,
) {}
```

#### Bindings/DateTimeBinding.php
```php
// Before
public function __construct(
    string $property,
    string $jsonField,
    bool $isRequired = false,
    string $dateTimeFormat = DateTimeInterface::ATOM
) {}

// After
public function __construct(
    string $property,
    ?string $jsonField = null,
    bool $isRequired = false,
    private string $dateTimeFormat = DateTimeInterface::ATOM
) {}
```

#### Exceptions/JsonValueException.php
```php
// Before
public function __construct($propertyName) {}

// After
public function __construct(string $propertyName) {}
```

#### Property.php
```php
// Before
private function __construct($instance, string $propertyName, ReflectionProperty $property = null) {}

// After
private function __construct(
    private object $instance,
    private string $propertyName,
    private ?ReflectionProperty $property = null,
) {}
```

### Signature changes of public methods

#### All "binding classes" (`Binding.php`, `Bindings/*Binding.php`)
```php
// Before
(abstract) public function bind(JsonDecoder $jsonDecoder, ?array $jsonData, Property $property);

// After
(abstract) public function bind(JsonDecoder $jsonDecoder, Property $property, array $jsonData = []): void;
```

#### ClassBindings.php
```php
// Before
public function decode(array $data, $instance) {}
public function register($binding) {}
public function hasBinding($property) {}

// After
public function decode(array $data, object $instance): mixed {}
public function register(Binding $binding): void {}
public function hasBinding(string $property): bool {}
```

#### JsonDecoder.php
```php
// Before
public function register(Transformer $transformer) {}
public function scanAndRegister(string $class) {}
public function decode(string $json, string $classType, string $root = null) {}
public function decodeMultiple(string $json, string $classType, string $root = null) {}
public function decodeArray($jsonArrayData, $classType) {}

// After
public function register(Transformer $transformer): void {}
public function scanAndRegister(string $class): void {}
public function decode(string $json, string $classType, string $root = null): mixed {}
public function decodeMultiple(string $json, string $classType, string $root = null): array {}
public function decodeArray(?array $jsonArrayData, string $classType): mixed {}
```

#### Property.php
```php
// Before
public static function create($instance, string $propertyName) {}
public function set($value) {}
public function getName() {}

// After
public static function create(object $instance, string $propertyName): self {}
public function set(mixed $value): void {}
public function getName(): string {}
```

#### Transformer.php
```php
// Before
public function register(ClassBindings $classBindings);
public function transforms();

// After
public function register(ClassBindings $classBindings): void;
public function transforms(): string;
```

### Removed class

* `Exceptions/InvalidBindingException.php`
