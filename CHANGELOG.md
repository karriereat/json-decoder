# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.2.1] - 2019-03-05
### Added
- `validate` function for checking the `isRequired` flag for Bindings

### Changed
- transform `Binding` to an abstract class and unify functionality from `AliasBinding`, `ArrayBinding` & `FieldBinding`

### Fixed
- check for `isRequired` flag is only executed when applicable

## [2.2.0] - 2019-01-11
### Added
- `JsonDecoder` instance as second parameter to callback function signature for CallbackBindings