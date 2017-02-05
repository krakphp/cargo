# Cargo

Cargo is yet another container library. It's feature set closely follows Pimple; however, it's design is more modular so that it can be extended. It was designed to be compatible with Pimple; so you can easily use any Pimple service providers with Cargo.

Pimple is a great service container; however it suffers from one problem... extendability. Pimple was never designed to properly extended or decorated which makes it very hard to add features without modifying the core. Cargo is a container that manages to keep the simplicity of Pimple while allowing powerful extensions.

## Installation

Install with composer at `krak/cargo`

## Usage

```php
<?php

use Krak\Cargo;

$c = Cargo\container();
$c['service'] = function($c) {
    return new Acme\Service($c['value']);
};
$c->factory('service_factory', function() {
    return new Acme\ServiceB();
});
$c->wrap('service', function($service, $c) {
    return new Acme\DecoratedService($service);
});
$c['value'] = 'some-value';
$c->protect('value', function() {});
$box = $c->box('service'); // get the Box for the service/value

assert($c['service'] instanceof Acme\DecoratedService());
```

## Pimple Compatibility

Achieving Pimple compatibility is simple with the `toPimple` function.

```php
<?php

use Krak\Cargo;

$pimple = Cargo\toPimple($c); // or $c->toPimple()

$pimple['a'] = function() {};
$pimple->extend('a', function() {});
$pimple['b'] = $pimple->protect(function() {

});

// $c has access to all services defined in pimple
$c['b'];
```

In this example, `$pimple_instance` is a an actual instance of `Pimple\Container`. However, all definitions will be stored in the original container `$c`.

## Container Interop

`Krak\Cargo\Container` is not compatible with the `ContainerInterop` interface by default. However, you can easily export the container to an Interop container using the `toInterop` function.

```php
<?php

use Krak\Cargo;

$interop = Cargo\toInterop($c); // or $c->toInterop
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
