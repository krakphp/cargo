<?php

namespace Krak\Cargo;

use Krak\Arr;

/** Default box factory that will construct a box appropriately from the options and it's value */
function stdBoxFactory() {
    return function($box, array $opts = []) {
        if ($box instanceof \Closure) {
            $box = new Box\LazyBox($box);
        } else if (!$box instanceof Box) {
            $box = new Box\ValueBox($box);
        }

        return $box;
    };
}

function wrap(Container $container, $id, $wrapper) {
    if (!$container->has($id)) {
        throw new \RuntimeException("Service '$id' has not been defined.");
    }

    return $container->add($id, new Box\WrappedBox(
        $container->box($id),
        $wrapper
    ), ['wrapped' => true]);
}

function protect(Container $container, $id, $value) {
    return $container->add($id, new Box\ValueBox($value));
}

function factory(Container $container, $id, $factory = null) {
    return $container->add($id, $factory, ['singleton' => false]);
}

function singleton(Container $container, $id, $factory = null) {
    return $container->add($id, $factory, ['singleton' => true]);
}

function alias(Container $container, $id, ...$aliases) {
    foreach ($aliases as $alias) {
        $container->add($alias, null, ['alias' => true, 'alias_of' => $id]);
    }
}

function env(Container $container, $var_name, $id = null) {
    return $container->add($id ?: $var_name, new Box\EnvBox($var_name));
}
