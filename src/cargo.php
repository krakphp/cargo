<?php

namespace Krak\Cargo;

/** Wraps a box instance */
function wrap(Container $c, $id, $value) {
    $old_box = $c->box($id);
    if (Container\optsAlias($old_box[1])) {
        return wrap($c, $old_box[0], $value);
    }

    $c->add($id, $value, array_merge(
        $old_box[1],
        ['wrapped' => $old_box]
    ));
    return $c;
}

/** adds or replaces a service */
function define(Container $c, $id, $value, array $opts = []) {
    if ($c->has($id)) {
        return replace($c, $id, $value, $opts);
    } else {
        $c->add($id, $value, $opts);
        return $c;
    }
}

/** Replaces a box. This is the same as add, except it will make sure that any wrapped definitions will stay wrapped */
function replace(Container $c, $id, $value, array $opts = []) {
    $old_box = $c->box($id);
    if (Container\optsAlias($old_box[1])) {
        return replace($c, $old_box[0], $value);
    }
    if (!isset($old_box[1]['wrapped'])) {
        $c->add($id, $value, array_merge($old_box[1], $opts));
        return $c;
    }

    $cur_box = &$old_box;
    while (isset($cur_box[1]['wrapped'][1]['wrapped'])) {
        $cur_box = &$cur_box[1]['wrapped'];
    }

    $cur_box[1]['wrapped'] = [$value, array_merge($cur_box[1]['wrapped'][1], $opts)];
    $c->add($id, $old_box[0], $old_box[1]);
    return $c;
}

function env(Container $c, $id, $env_var = null) {
    $env_var = $env_var ?: $id;
    $c->add($id, $env_var, [
        'env' => true,
    ]);
    return $c;
}

function factory(Container $c, $id, $value = null) {
    $c->add($id, $value ?: $id, ['factory' => true, 'service' => true]);
    return $c;
}

function singleton(Container $c, $id, $value = null) {
    $c->add($id, $value ?: $id, ['factory' => false, 'service' => true]);
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

function containerFactory() {
    return new ContainerFactory();
}

function buildCachedParams(Container $container, $id, array $params_def, array $params) {
    foreach ($params_def as $key => $def) {
        if (array_key_exists($key, $params)) {
            continue;
        }
        if (!$def['has_value']) {
            throw new Exception\ContainerException("Service $id requires parameter $key to be set when building.");
        }

        $params[$key] = $def['type'] == 'service'
            ? $container->get($def['value'])
            : $def['value'];
    }

    return $params;
}
