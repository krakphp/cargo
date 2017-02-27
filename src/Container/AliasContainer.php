<?php

namespace Krak\Cargo\Container;

use Krak\Cargo,
    Krak\Arr;

/** Freezes a service definition after it's been used preventing an override of that
    service */
class AliasContainer extends AbstractContainer
{
    private $aliases;

    public function __construct(Cargo\Container $container) {
        $this->container = $container;
        $this->aliases = [];
    }

    public function get($id, Cargo\Container $container = null) {
        return $this->container->get($this->resolveId($id), $container ?: $this);
    }
    public function has($id) {
        return $this->container->has($this->resolveId($id));
    }
    public function remove($id) {
        // just remove the alias if that's what it is
        if (isset($this->aliases[$id])) {
            unset($this->aliases[$id]);
            return;
        }

        // check if we are removing an aliased id, if so, we need to remove all aliases as well
        $aliases_to_remove = [];
        foreach ($this->aliases as $alias => $identifier) {
            if ($id == $identifier) {
                $aliases_to_remove[] = $alias;
            }
        }

        foreach ($aliases_to_remove as $alias) {
            unset($this->aliases[$alias]);
        }

        return $this->container->remove($id);
    }
    public function add($id, $box, array $opts = []) {
        if (Arr\get($opts, 'alias', false)) {
            $this->aliases[$id] = $opts['alias_of'];
            return;
        }

        return $this->container->add($id, $box, $opts);
    }
    public function box($id) {
        return $this->container->box($this->resolveId($id));
    }
    public function keys() {
        return array_merge($this->container->keys(), array_keys($this->aliases));
    }

    private function resolveId($id) {
        while (isset($this->aliases[$id])) {
            $id = $this->aliases[$id];
        }

        return $id;
    }
}
