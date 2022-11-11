# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [4.2.0] - 2022-11-11
### Added
- Support for using array-keys in `ArrayBinding` (#52, #55)

## [4.1.1] - 2022-01-17
### Fixed
- Avoid IDE warnings when passing `Closure` to `CallbackBinding`

## [4.1.0] - 2021-10-20
### Added
- Support for PHP 8

## [4.0.3] - 2020-11-30
### Fixed
- To strict param type in `transform` method

## [4.0.2] - 2020-10-15
### Fixed
- Another issue where CallbackBinding wasn't handled.

## [4.0.1] - 2020-10-15
### Fixed
- Still handle CallbackBinding when property name doesn't match a JSON fieldname.

## [4.0.0] - 2020-10-06
### Added
- Support for magic class properties
- Auto casing for json field - class properties mapping
- PHP CS Fixer for linting and fixing code style

### Changed
- Unit tests to phpunit

### Removed
- Support for PHP 7.2

## [3.1.0] - 2020-04-10
### Added
- `DateTimeBinding` for parsing date string

## [3.0.0] - 2020-03-25
### Changed
- Update dependencies to be compatible with PHP 7.4
- Switch to PSR-12 linting

### Removed
- Support for PHP 7.0 and 7.1

## [2.2.1] - 2019-03-05
### Added
- `validate` function for checking the `isRequired` flag for Bindings

### Changed
- Transform `Binding` to an abstract class and unify functionality from `AliasBinding`, `ArrayBinding` & `FieldBinding`

### Fixed
- Check for `isRequired` flag is only executed when applicable

## [2.2.0] - 2019-01-11
### Added
- `JsonDecoder` instance as second parameter to callback function signature for CallbackBindings
