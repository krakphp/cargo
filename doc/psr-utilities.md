---
currentMenu: psr-utilities
---
# PSR Utilities

Cargo ships with some PSR Utilites to help with container interoperability.

## Chain Container

You can use the `Krak\Cargo\Psr\ChainContainer` to create chained PSR Containers.

```php
$c = new Cargo\Psr\ChainContainer([
    Cargo\container([
        'a' => 1,
    ]),
    new SomeOtherPsrContainer([
        'b' => 1,
    ])
]);
assert($c->get('a') == $c->get('b'))
```

Here the `ChainContainer` will check each PSR Container if it has the item and will return it or throw an exception if no container has the item.

## Wrap Array Access Container

`Krak\Cargo\Psr\WrapArrayAccessContainer` can be useful for wrapping any ArrayAccess type container or array. For example, this can be used to wrap a Pimple instance.

```php
$c = new Cargo\Psr\WrapArrayAccessContainer(new Pimple\Container([
    'a' => 1,
]));
assert($c->get('a') == 1);
```
