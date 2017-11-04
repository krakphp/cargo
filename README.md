# Cargo

[![Author](https://img.shields.io/badge/author-%40ragboyjr-blue.svg)](https://twitter.com/ragboyjr)
![Release](https://img.shields.io/badge/release-v0.3--dev-blue.svg)

Cargo is yet another service container library that strives for simplicity with powerful extensions. It facilitates IoC and DI by providing a streamlined API designed to be extended. Its feature set and semantics closely follow [Pimple](http://pimple.sensiolabs.org) and the [Laravel Service Container](https://laravel.com/docs/5.5/container); however, its design is more much modular so that it can extended to fit any need.

- [Features](#features)
- [Installation](#installation)
- [Basic Usage](#basic-usage)
- [Container](#container)
    - [Defining Services/Values](#defining-services-values)
    - [Accessing Services/Values](#accessing-services-values)
    - [Environment Parameters](#environment-parameters)
    - [Wrapping Services](#wrapping-services)
    - [Aliases](#aliases)
- [Auto Wiring](#auto-wiring)
- [Service Providers](#service-providers)
- [Performance](#performance)
    - [Lazy Loading Service Providers](#lazy-loading-service-providers)
    - [Caching Auto Wired Services](#caching-auto-wired-services)
- [PSR Utilities](#psr-utilities)
- [Extending Cargo](#extending-cargo)
    - [Container Interface](#container-interface)
    - [Decorators](#decorators)
    - [Unboxing](#unboxing)
- [API](#api)
    - [Interfaces](#interfaces)
    - [Container Factories](#container-factories)
    - [Container Functions](#container-functions)
    - [Containers](#containers-1)
    - [Unboxing](#unboxing-1)
    - [Exceptions](#exceptions)
    - [Psr Utilities](#psr-utilities-1)
- [Development](#development)

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
- Zero dependencies
- and so much more!

## Installation

Install with composer at `krak/cargo`.

Cargo is compatible with php 5.6+ and 7.0+.

## Usage

```php
<?php

use Krak\Cargo;

$c = Cargo\container();
$c->add('service.parameter', 'value');
$c->singleton(AcmeService::class, function($c) {
    return new AcmeService($c['service.parameter']);
});

$acme_service = $c->get(AcmeService::class);
```

### Creating Containers

There are several ways to create cargo containers. The easiest way is to just create the default container like so:

```php
<?php

use Krak\Cargo;

$c = Cargo\container();
// same as doing
$c = new Cargo\Container\BoxContainer();
```

### Services and Parameters

Services are defined by creation functions using closures.

```php
$c->add('service', function($container) {
    return new Service($container->get('serviceB'))
});
```

This creation function is not invoked until you try to access the service at a later time. Each service creation function gets passed two values: The container instance, and an array of parameters. You can use both to construct your services any way you need.

To access a service, you can use:

```php
$c->get('service');
```

### Accessing the Container


### Environment Parameters

### Wrapping Services

### Aliases

### Auto Wiring

### Service Providers

### PSR Utilities

### Tuning for Production

### Lazy Loading Service Providers

### Caching Auto Wired Services

## API

### Container Factories

##### container(array $values = [])

##### containerFactory()

### Container Functions

##### wrap(Container $c, $id, $value)

##### define(Container $c, $id, $value, array $opts = [])

##### replace(Container $c, $id, $value, array $opts = [])

##### env(Container $c, $id, $env_var = null)

##### factory(Container $c, $id, $value = null)

##### singleton(Container $c, $id, $value = null)

##### alias(Container $c, $id, ...$aliases)

##### fill(Container $c, array $values)

##### protect(Container $c, $id, $value)


#### interface Container
#### interface Unbox
#### class ContainerFactory

### Defining Services

Services can defined and configured several ways.

```php
$c['a'] = function($c) {
    return new ServiceA();
};
// or
$c->add('b', function($c) {
    return new ServiceB($c['a']);
});
```

Due to the BoxFactoryContainer, all Closures are treated as lazy services. Meaning, they are not invoked until needed. The Singleton container also defaults all services to be singletons, so the result of the service definition closure is cached so that it's not invoked twice. These semantics mimic the behavior of the [Pimple Container](http://pimple.sensiolabs.org);

### Accessing the Container

You can either use the ArrayAccess methods or `get` to retrieve values and invoke services.

```
$c['a'] == $c->get('a');
```

### Factory or Singleton Services

You can specify if you want to define a service as a factory or singleton with these two helper methods.

```php
$c->singleton('a', function() {
    return new ServiceA();
});
$c->factory('b', function() {
    return new ServiceB();
});
// $c['a'] === $c['a'] - same instance each time
// $c['b'] !== $c['b'] - different instance each time
```

### Parameters/Values

Anything added to the container that isn't a service is defined as value.

```php
$c['a.parameter'] = 'value';
$c['a'] = function($c) {
    return new ServiceA($c['a.parameter']);
};
```

Values are stored and retrieved as is. No processing is done to them.

If you want to use a closure as a parameter, you can use the `protect` method:

```php
$func = function() {};
$c->protect('a.closure_parameter', $func);
// it returns the same instance because values are just stored as is.
// $c['a.closure_parameter'] === $func
```

### Env Parameters

You can register parameters to be read from the environment with the `env` method:

```php
$c->env('APP_KEY', $alias = 'application.key');
// $c['APP_KEY'] === $c['application.key'] are read from the env
```

### Wrapping Services

Similar to Pimple's `extend`, Cargo allows you to wrap service definitions for decoration.

If you want to replace a definition, you would simply redefine it; however, if you want to decorate or modify a definition, you wrap it:

```php
$c['logger'] = function() {
    return new Logger();
};
$c->wrap('logger', function($logger, $c) {
    $logger->setValue($c['value']);
    return new MyLogger($logger);
});
// $c['logger'] instanceof MyLogger == true
```

### Service Freezing

Services by default will be frozen due to the FreezingContainer. You can redefine entries as much you'd like, but once a service is invoked, it is considered frozen and will throw an exception if you try to redefine it.

```php
$c['a'] = function() {};
// ok to redefine because we haven't invoked 'a' yet.
$c['a'] = function() {};
$service = $c['a'];
// this will throw an exception because the service was frozen
$c['a'] = function() {};
```

### Aliasing Entries

It's often useful to use class names as the identifier, but then also provide aliases for a quick reference.

```php
$c[Acme\ServiceA::class] = function() {
    return new Acme\ServiceA();
};
$c->alias(Acme\ServiceA::class, 'acme.service_a', 'a');
// $c[Acme\ServiceA::class] === $c['service_a'] === $c['a']
```

### Auto Wiring

Auto wiring allows the container to try and automatically instantiate services if they aren't already defined in the container. To enable auto-wiring, you need to:

1. Install the [Auto Args](https://github.com/krakphp/auto-args) Library (`composer install krak/auto-args`)
2. Use the AutoWireContainer

```php
// the second parameter as true will include the auto wiring
$c = Cargo\container([], $auto_wire = true);
$stack = $c->get('SplStack');
// will return an instance of SplStack as a singleton.

// defines 'StdClass' as a factory instance and will set it up for auto-wiring since no definition was given.
$c->factory('StdClass');
// $c['StdClass'] !== $c['StdClass']
```

In addition, you can bind any class to be auto wired:

```php
$c->singleton('a', SplStack::class);
$c->factory('b', ArrayObject::class);
```

`a` and `b` will resolve to their respective classes. This only works on singleton/factory entries, else it'll just treat the service like a string value and won't try to auto-resolve it.

### Service Providers

`Cargo\ServiceProvider` provides a simple interface for defining multiple related services.

```php
interface ServiceProvider {
    public function register(Cargo\Container $c);
}
```

You can register service providers with a given container with the `register` method:

```php
$c->register(new FooProvider(), [
    'foo.parameters' => 1,
]); // or Cargo\register($c, new FooProvider(), [])
```

### Container Interop

`Krak\Cargo\Container` is not compatible with the `ContainerInterop` interface by default. However, you can easily export the container to an Interop container using the `toInterop` function.

```php
$interop = Cargo\toInterop($c); // or $c->toInterop
// $interop instanceof Psr\Container\ContainerInterface
```

### Pimple Interop

Achieving Pimple compatibility is simple with the `toPimple` function.

```php
$pimple = Cargo\toPimple($c); // or $c->toPimple()

$pimple['a'] = function() {};
$pimple->extend('a', function() {});
$pimple['b'] = $pimple->protect(function() {

});

// $c has access to all services defined in pimple
$c['b'];
```

### Delegate Containers

In an effort to provide better integration with other containers, we provide delegate containers to allow you to default to a cargo definitions, but fallback to the delegate container.

`ArrayAccessDelegateContainer` and `PsrDelegateContainer` both act as delegate containers. The first will accept any array or `ArrayAccess` object (like Pimple), and the other will accept any Psr Container.

```php
<?php

$pimple = new Pimple\Container();
$pimple['a'] = 1;
$pimple['b'] = 1;
$c = Cargo\container();
$c = new Cargo\Container\ArrayAccessDelegateContainer($c, $pimple);
$c['b'] = 2;

assert($c['b'] == 2 && $c['a'] == 1);
```

## Cargo Design

### Container Interface for Decoration

To do...

### Boxes

To do...

## API

### function alias(Container $c, $id, ...$aliases)

Aliases an entry `$id` into `$aliases` for the container `$c`. Each alias will share the same box reference as the original entry.

### function env(Container $c, $var_name, $id = null)

Adds an EnvBox entry into the container `$c` with `$var_name` being the name of the env var and `$id` is the entry name.
If `$id` is left null, then it will default to `$var_name`.
