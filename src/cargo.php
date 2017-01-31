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
function container(array $values = []) {
    $c = new Container\FreezingContainer(new Container\BoxContainer());
    $c->fill($values);
    return $c;
}
