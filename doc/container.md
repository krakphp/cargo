---
currentMenu: container
---
# Container

- [Creating Containers](#creating-containers)
- [Defining Services/Values](#defining-services-values)
- [Accessing Services/Values](#accessing-services-values)
- [Environment Parameters](#environment-parameters)
- [Wrapping Services](#wrapping-services)
- [Aliases](#aliases)

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

## Defining Services/Values

The container's primary purpose is to store service definitions and subsequently build and retrieve the defined services. A stored service or value is referred to by a box which as the name suggests, is just a storage unit for the service or value. When the box is accessed from the container, it's called unwrapping the box.

Services are defined by factory functions which are used to build/create the service when the box is unwrapped. Values are simply values stored in the container and will be unboxed *as is*.

```php
$c->add('service', function($container, $parameters) {
    return new Service($container->get('parameter'));
});
$c->add('parameter', 1);
```

This factory function is not invoked until you try to access the service at a later time. Each service creation function gets passed two values: The container instance, and an array of parameters. You can use both to construct your services any way you need.

To access a service, you can use:

```php
$c->get('service');
```


### Accessing the Container


### Environment Parameters

### Wrapping Services

### Aliases
