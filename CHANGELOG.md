# Change Log

## Unreleased

### Added

- Cloneable Containers #11

## 0.2.4 - 2017-04-26

### Added

- Delegate Containers #9
- cachingBoxFactory which will by default cache lazy boxes
- liteContainer for a lightweight container for testing

## 0.2.3 - 2017-03-28

### Added

- Ability to bind classes to entries to be auto resolved.

## 0.2.2 - 2017-03-27

### Added

- Better error messages for auto-wire resolution issues #3

### Fixed

- Ability to wrap aliased services #2
- Bug in InteropWrapper which incorrectly would convert all exceptions into container exceptions.

## 0.2.1 - 2017-03-11

### Added

- `Container\ContainerDecorator` to help with creating container decorators.
- `Container\AuxillaryMethodsTrait` to implement all of the auxillary methods
  in an object oriented manner.

### Changed

- Updated all of the container decorators to use the `ContainerDecorator` class

## 0.2.0 - 2017-02-27

### Changed

- Container interface to allow for greater extendability
- Simplified the BoxContainer and split functionality into several Containers: AliasContainer, BoxFactoryContainer, FreezingContainer,
SingletonContainer
- Updated the interop wrapper to use the Psr Container

### Fixed

- Bug within `container` creation function
- Bug for `InteropWrapper`

### Added

- `alias` for aliasing boxes
- `env` for accessing values from the env
- Added a new countable trait
- Added more and more tests

## 0.1.0 - 2017-01-29

### Added

- Initial Implementation
- Containers
- Pimple Wrapper
- Container Interop Wrapper
- Initial Documentation
