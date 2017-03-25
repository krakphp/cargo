# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

### Fixed

- ability to wrap aliased services #2

## [0.2.1] - 2017-03-11
### Added

- `Container\ContainerDecorator` to help with creating container decorators.
- `Container\AuxillaryMethodsTrait` to implement all of the auxillary methods
  in an object oriented manner.

### Changed

- Updated all of the container decorators to use the `ContainerDecorator` class

## [0.2.0] - 2017-02-27

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

## [0.1.0] - 2017-01-29
### Added

- Initial Implementation
- Containers
- Pimple Wrapper
- Container Interop Wrapper
- Initial Documentation
