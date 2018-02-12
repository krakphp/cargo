---
currentMenu: home
---
# Cargo

[![Author](https://img.shields.io/badge/author-%40ragboyjr-blue.svg)](https://twitter.com/ragboyjr)
![Release](https://img.shields.io/badge/release-v0.3--dev-blue.svg)

Cargo is yet another service container library that strives for simplicity with powerful extensions. It facilitates IoC/DI while providing a streamlined API designed for useability and proper composition.

Cargo's philosophy is to provide a simple interface and implementation to provide the basic functionality of an IoC container and provide the other amazing features as optional extensions. If you don't want Automatic Dependency Injection or Environment Variable Parameters, you don't have to use them.

## Features

- Simple API, Awesome extendability
- Container Decorators galore to provide or implement any feature you want
- Support for Environment Parameters
- Auto Wired Services (Automatic Dependency Injection) for development
- Ability to cache Auto Wired services for production
- Lazy Loading of Service Providers
- PSR-11 Compliant
- Integrates well with Pimple and other PSR-11 containers via the PSR utilities
- Cycles Detection Container that will catch any circular dependencies and prevent infinite loops!
- and so much more!

## Installation

Install with composer at `krak/cargo`.

Cargo is compatible with php 5.6+ and 7.0+.

## Basic Usage

```php
<?php

use Krak\Cargo;

$c = Cargo\container();
$c->add('service.parameter', 'value');
Cargo\singleton($c, AcmeService::class, function($c) {
    return new AcmeService($c['service.parameter']);
});
Cargo\factory($c, FooService::class, function($c) {
    return new FooService();
});

$acme_service = $c->get(AcmeService::class); // same instance will be returned each time
$foo_service = $c->get(FooService::class); // new instance will be created each time
```
