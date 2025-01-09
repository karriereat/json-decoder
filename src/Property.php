<?php

namespace Karriere\JsonDecoder;

use AllowDynamicProperties;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;

#[AllowDynamicProperties]
class Property
{
    private function __construct(
        private object $instance,
        private string $propertyName,
        private ?ReflectionProperty $property = null,
    ) {
    }

    public static function create(object $instance, string $propertyName): self
    {
        $property = null;

        try {
            $property = new ReflectionProperty($instance, $propertyName);
            $property->setAccessible(true);
        } catch (ReflectionException) {
        }

        return new self($instance, $propertyName, $property);
    }

    public function set(mixed $value): void
    {
        if (is_null($this->property)) {
            if ($this->dynamicPropertiesAllowed()) {
                $this->instance->{$this->propertyName} = $value;
            }
        } else {
            try {
                $property = new ReflectionProperty(get_class($this->instance), $this->propertyName);

                if ($this->valueHasCorrectType($property, $value)) {
                    $property->setAccessible(true);
                    $property->setValue($this->instance, $value);
                }
            } catch (ReflectionException) {
            }
        }
    }

    private function dynamicPropertiesAllowed(): bool
    {
        if (version_compare(PHP_VERSION, '8.2.0', '<')) {
            return true;
        }

        $classAttributes = (new ReflectionClass($this->instance))->getAttributes();

        foreach ($classAttributes as $attribute) {
            if ($attribute->getName() === 'AllowDynamicProperties') {
                return true;
            }
        }

        return false;
    }

    public function getName(): string
    {
        return $this->propertyName;
    }

    private function valueHasCorrectType(ReflectionProperty $property, mixed $value): bool
    {
        if (! $property->hasType()) {
            return true;
        }

        if ($this->setToNullIfAllowed($property->getType(), $value)) {
            return true;
        }

        if ($this->typesMatch($property->getType(), $value)) {
            return true;
        }

        return false;
    }

    private function setToNullIfAllowed(?ReflectionType $type, mixed $value): bool
    {
        return $type?->allowsNull() && is_null($value);
    }

    private function typesMatch(?ReflectionType $reflectionType, mixed $value): bool
    {
        $valueType = is_object($value) ? get_class($value) : get_debug_type($value);

        if ($reflectionType instanceof ReflectionNamedType) {
            if ($reflectionType->getName() === $valueType) {
                return true;
            }
        }

        if ($reflectionType instanceof ReflectionUnionType) {
            foreach ($reflectionType->getTypes() as $type) {
                if ($type instanceof ReflectionNamedType) {
                    if ($type->getName() === $valueType) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
