<?php

namespace Krak\Cargo\Container;

use Krak\Cargo;
use function in_array, array_key_exists, array_merge;

final class AliasContainer extends ContainerDecorator
{
    private $aliases = [];

    public function make($id, array $params = [], Cargo\Container $c = null) {
        return $this->container->make($this->resolveAlias($id), $params, $c ?: $this);
    }

    public function add($id, $box, array $opts = []) {
        if (isset($opts['alias']) && $opts['alias']) {
            $this->aliases[$id] = $box;
            return;
        }

        return $this->container->add($this->resolveAlias($id), $box, $opts);
    }

    public function has($id) {
        return array_key_exists($id, $this->aliases) ? true : $this->container->has($id);
    }

    public function remove($id) {
        if (array_key_exists($id, $this->aliases)) {
            unset($this->aliases[$id]);
            return;
        }

        return $this->container->remove($id);
    }

    public function box($id) {
        return $this->container->box($this->resolveAlias($id));
    }

    public function resolveAlias($id, $resolved = []) {
        if (in_array($id, $resolved)) {
            throw new Cargo\Exception\CycleDetectedException(array_merge($resolved, [$id]));
        }

        return array_key_exists($id, $this->aliases)
            ? $this->resolveAlias($this->aliases[$id], array_merge($resolved, [$id]))
            : $id;
    }

    public function count() {
        return $this->container->count() + count($this->aliases);
    }
    public function keys() {
        return array_merge($this->container->keys(), array_keys($this->aliases));
    }
}
