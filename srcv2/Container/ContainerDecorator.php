<?php

namespace Krak\CargoV2\Container;

use Krak\CargoV2;

/** Stores values for the containers */
abstract class ContainerDecorator extends AbstractContainer
{
    protected $container;

    public function __construct(CargoV2\Container $container) {
        $this->container = $container;
    }

    public function __clone() {
        $this->container = clone $this->container;
    }

    public function make($id, array $params = [], CargoV2\Container $c = null) {
        return $this->container->make($id, $params, $c ?: $this);
    }
    public function remove($id) {
        return $this->container->remove($id);
    }
    public function add($id, $box, array $opts = []) {
        return $this->container->add($id, $box, $opts);
    }
    public function has($id) {
        return $this->container->has($id);
    }
    public function box($id) {
        return $this->container->box($id);
    }
    public function keys() {
        return $this->container->keys();
    }
    public function count() {
        return $this->container->count();
    }
}
