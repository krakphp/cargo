<?php

namespace Krak\Cargo;

/** default to singleton boxes on closures */
function cachedBoxFactory() {
    return function($box) {
        if ($box instanceof Box) {
            return $box;
        } else if ($box instanceof \Closure) {
            return new Box\CachedBox(new Box\LazyBox($box));
        } else {
            return new Box\ValueBox($box);
        }
    };
}

/** default to factories for boxes on closures */
function factoryBoxFactory() {
    return function($box) {
        if ($box instanceof Box) {
            return $box;
        } else if ($box instanceof \Closure) {
            return new Box\LazyBox($box);
        } else {
            return new Box\ValueBox($box);
        }
    };
}

function wrap(Container $container, $id, $wrapper) {
    if (!$container->has($id)) {
        throw new \RuntimeException("Service '$id' has not been defined.");
    }

    return $container->add($id, new Box\WrappedBox(
        $container->box($id),
        $wrapper
    ));
}

function protect(Container $container, $id, $value) {
    return $container->add($id, new Box\ValueBox($value));
}

function factory(Container $container, $id, $factory) {
    return $container->add($id, new Box\LazyBox($factory));
}

function singleton(Container $container, $id, $factory) {
    return $container->add($id, new Box\LazySingletonBox($factory));
}

function alias(Container $container, $id, ...$aliases) {
    foreach ($aliases as $alias) {
        $container->add($alias, $container->box($id));
    }
}
