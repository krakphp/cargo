<?php

namespace Krak\CargoV2;

/** Wraps a box instance */
function wrap(Container $c, $id, $box) {
    $old_box = $c->box($id);
    $c->add($id, $box, array_merge(
        $old_box[1],
        ['wrapped' => $old_box]
    ));
    return $c;
}

function env(Container $c, $id, $env_var = null) {
    $env_var = $env_var ?: $id;
    $c->add($id, $env_var, [
        'env' => true,
    ]);
    return $c;
}

function factory(Container $c, $id, $box = null) {
    $c->add($id, $box ?: $id, ['factory' => true, 'service' => true]);
    return $c;
}

function singleton(Container $c, $id, $box = null) {
    $c->add($id, $box ?: $id, ['factory' => false, 'service' => true]);
    return $c;
}

function alias(Container $c, $id, ...$aliases) {
    foreach ($aliases as $alias) {
        $c->add($alias, $id, ['alias' => true, 'factory' => true]);
    }
    return $c;
}

function fill(Container $c, array $values) {
    foreach ($values as $key => $value) {
        protect($c, $key, $value);
    }
    return $c;
}

function protect(Container $c, $id, $value) {
    $c->add($id, $value, ['service' => false]);
    return $c;
}

function container(array $values = []) {
    $c = new Container\BoxContainer();
    fill($c, $values);
    return $c;
}
