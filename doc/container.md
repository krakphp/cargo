---
currentMenu: container
---
# Container

- [Creating Containers](#creating-containers)
- [Boxes](#boxes)
- [Accessing the Container](#accessing-the-container)
- [Singletons](#singletons)
- [Factory](#factory)
- [Protected Values](#protected-values)
- [Environment Parameters](#environment-parameters)
- [Aliases](#aliases)
- [Wrapping](#wrapping)
    - [Replacing](#replacing)
- [Syntactic Sugar](#syntactic-sugar)

## Creating Containers

There are several ways to create cargo containers. The easiest way is to just create the default container like so:

```php
<?php

use Krak\Cargo;

$c = Cargo\container();
// same as doing
$c = new Cargo\Container\BoxContainer();
```

You can also use the ContainerFactory which is a utility that will decorate and configure the containers to create the feature set you want:

```php
$c = Cargo\containerFactory()->autoWire()->detectCycles()->env()->create();
// or
$c = (new Cargo\ContainerFactory())->autoWire()->detectCycles()->env()->create();
```

And if you need even more customization, you can always just create the containers youself.

```php
$unbox = new Cargo\Unbox\ServiceUnbox();
$unbox = new Cargo\Unbox\EnvUnbox($unbox);
$c = new Cargo\Container\BoxContainer($unbox);
$c = new Cargo\Container\DetectCyclesContainer($c);
```

The main container is the BoxContainer which implements the basic functionality of an IoC container. It stores definitions (boxes) and uses those definitions to later on contruct services. The other containers are decorators that optionally supplement the feature set of Cargo.

## Boxes

Boxes as mentioned earlier are simple definitions or rather entries in the BoxContainer. They hold the ingredients to building a service. *Unboxing* is the term used for taking the box and constructing a service.

Boxes are implemented as 2-tuples which hold the Box value and any options related to the value.

You can add boxes to the Container via the `add` function.

```php
$c->add('service', function($container) {
    return new Service($container->get('parameter'));
});
$c->add('parameter', 'value');
```

In the above example, we added two boxes, one is defined a singleton service that has a factory function value. The other is a parameter which is basically just a raw value that won't get *unboxed*, it'll be returned as is. Note that the above is equivilant to the following due to the default behaviors.

```php
$c->add('service', function($container) {
    return new Service($container->get('parameter'));
}, ['service' => true, 'factory' => false]);
$c->add('parameter', 'value', ['service' => false]);
```

You can access stored values using the following


The container's primary purpose is to store service definitions and subsequently build and retrieve the defined services. A stored service or value is referred to by a box which as the name suggests, is just a storage unit for the service or value. When the box is accessed from the container, it's called unwrapping the box.

Services are defined by factory functions which are used to build/create the service when the box is unwrapped. Values are simply values stored in the container and will be unboxed *as is*.

```php
$c->add('service', function($container, $parameters) {
    return new Service($container->get('parameter'));
});
$c->add('parameter', 1);
```

This factory function is not invoked until you try to access the service at a later time. Each service creation function gets passed two values: The container instance, and an array of parameters (we'll talk more about those later). You can use both to construct your services any way you need.

To redefine a box, just simply add with the same name and it'll overwrite the previous definition.

## Accessing the Container

You can access the container in two ways:

```php
$c->get('service');
$c->make('service', $params = []); // params are optional
```

Both methods will return the configured service. The only difference between `make` and `get` is that `make` will pass that array of parameters down for when the service is constructed.

```php
$c->add(Acme\Service::class, function($c, $params) {
    return new Acme\Service($params['arg1']);
});
$service = $c->make(Acme\Service::class, ['arg1' => 'value']);
```

Passing arguments to services defined manually don't make a whole lot of sense, but will come into play later when we look at the AutoWiring extension.

## Singletons

You can define singleton services which are created only one time and any subsequent requests for the same service will return the same exact instance.

```php
Cargo\singleton($c, 'service', function() {
    // this function will only be called once
    return new Service();
});
assert($c->get('service') === $c->get('service'));
```

## Factory

You can also define factory services which are created fresh every time for every request for the service.

```php
Cargo\factory($c, 'service', function() {
    // this function will be called twice
    return new Service();
});
assert($c->get('service') !== $c->get('service'));
```

## Protected Values

There may be times where you want to store callables and *not* have them treated as service definitions. In that case, you can just use `protect`.

```php
$myFunc = function() {};
Cargo\protect($c, 'value', $myFunc);
assert($c->get('value') === $myFunc);
```

## Environment Parameters

It's fairly common to want to access environment variables when configuring services. `env` provides a simple way to register env variables that are accessed lazily when the time comes.

```php
Cargo\env($c, 'API_KEY'); // no getenv call is made here
$c->get('API_KEY'); // API_KEY is fetched from the environment at this point
```

You can also set a custom env var name if you'd like:

```php
Cargo\env($c, 'apiKey', 'API_KEY');
$c->get('apiKey'); // returns the API_KEY env variable
```

Environment parameters are not enabled by default. They can be added by manually constructing the Unbox with the `EnvUnbox`, or by using the Container factory.

```php
$c = Cargo\containerFactory()->env()->create();
```

## Aliases

Cargo let's you define aliases for any defined box. This is very useful for creating shorthand aliases for a longer service name or maybe for backwards compatibility if you want to migrate old usage of a service name to something else.

```php
Cargo\singleton($c, 'Doctrine\ORM\EntityManagerInterface', function() {
    // return an entity manager
});
Cargo\alias($c, 'Doctrine\ORM\EntityManagerInterface', 'Doctrine\Common\Persistence\ObjectManager', 'em');
$c->get('Doctrine\ORM\EntityManagerInterface') === $c->get('Doctrine\Common\Persistence\ObjectManager') === $c->get('em');
```

Aliases are not enabled by default. They can be added by manually added via the `AliasContainer` decorator or by the Container factory.

```php
$c = Cargo\containerFactory()->alias()->create()
```

## Wrapping

When you need to extend/alter a box, you can do so by wrapping. Wrapping allows you to decorate or configure a service when it has been accessed.

```php
Cargo\singleton($c, 'service', function() {
    return new Service();
});
Cargo\wrap($c, 'service', function(Service $service, Cargo\Container $c) {
    return new DecoratedService($service);
});

assert($c->get('service') instanceof DecoratedService);
```

In this instance, when we unboxed `service`, the initial definition was called, and then the wrapper was invoked with that definition. If multiple wraps are called on a `service`, they will be executed in the order of call.

Also, it's important to note that wrapping follows the same instantiation rules as whatever the service was originally defined as. So, if the box is a singleton, then the wrappers will only be called once, if factory, they will be called on every instantiation.

### Replacing

Consider the following code:

```php
Cargo\singleton($c, 'service', function() {
    return new Service();
});
Cargo\wrap($c, 'service', function($s) {
    return new DecoratedService($s);
});
Cargo\singleton($c, 'service', function() {
    return new AcmeService();
});
assert($c->get('service') instanceof AcmeService);
```

When you redefine a box, the new box will overwrite the old which also includes any previous wrapped definitions.

If you want to redefine a service *and* keep the wrapped definitions, you can use `replace`.

```php
Cargo\singleton($c, 'service', function() {
    return new Service();
});
Cargo\wrap($c, 'service', function($s) {
    return new DecoratedService($s);
});
Cargo\replace($c, 'service', function() {
    return new AcmeService();
});
assert($c->get('service') instanceof DecoratedService);
```

We also provide a helper function `define` which will add the box if it doesn't exist or replace it if it does.

## Syntactic Sugar

Cargo maintains a simple API while allowing powerful abstractions. None of the core cargo functions like `singleton`, `wrap`, `replace`, etc... get any special access to the cargo containers. This means that any of these awesome features in Cargo can be modified or extended in third party customizations. The only downside to this design is that it makes using the Cargo container more cumbersome than if the methods were defined directly on the containers. To mitigate this, we've added the ability to for a more OO interface via Container Methods.

Container methods are registered via:

```php
Cargo\registerContainerMethods([
    'singleton' => 'Krak\\Cargo\\singleton',
]);
```

Each container method needs to accept a `Krak\Cargo\Container` as it's first parameter, and everything else is just forwarded to the registered method.

Once registered you can access the `singelton` method on the container itself:

```php
// normal
Cargo\singleton($c, 'service', function() {});

// with container method
$c->singleton('service', function() {});
```

By default, the methods like `singleton`, `env`, `factory`, etc.. are **not registered as container methods**. To register them, you can just call `Krak\Cargo\bootstrapContainerMethods()`.
