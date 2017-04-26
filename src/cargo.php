<?php

namespace Krak\Cargo;

function fill(Container $c, array $values) {
    foreach ($values as $k => $v) {
        $c[$k] = $v;
    }
}

function register(Container $c, ServiceProvider $provider, array $values = []) {
    $provider->register($c);
    fill($c, $values);
}

function toPimple(Container $c) {
    return new Container\Wrapper\PimpleWrapper($c);
}

function toInterop(Container $c) {
    return new Container\Wrapper\InteropWrapper($c);
}

/** returns the default container */
function container(array $values = [], $auto_wire = false) {
    $c = new Container\BoxContainer();
    $c = new Container\SingletonContainer($c);
    $c = new Container\BoxFactoryContainer($c);
    $c = new Container\FreezingContainer($c);
    if ($auto_wire) {
        $c = new Container\AutoWireContainer($c);
    }
    $c = new Container\AliasContainer($c);
    $c->fill($values);
    return $c;
}

/** simple storage container without any awesome features. Use this if you want something lightweight (great for testing). */
function liteContainer(array $values = [], $box_factory = null) {
    $c = new Container\BoxContainer();
    $c = new Container\BoxFactoryContainer($c, $box_factory ?: cachingBoxFactory());
    $c->fill($values);
    return $c;
}
