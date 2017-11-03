<?php

namespace Krak\Cargo\Container;

use Krak\Cargo;

/** Stores values for the containers */
abstract class ContainerDecorator extends AbstractContainer
{
    protected $container;

    public function __construct(Cargo\Container $container) {
        $this->container = $container;
    }

    public function __clone() {
        $this->container = clone $this->container;
    }

    public function make($id, array $params = [], Cargo\Container $c = null) {
        return $this->container->make($id, $params, $c ?: $this);
    }
    public function remove($id) {
        return $this->container->remove($id);
    }
    public function add($id, $value, array $opts = []) {
        return $this->container->add($id, $value, $opts);
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
    public function register(Cargo\ServiceProvider $provider, Cargo\Container $c = null) {
        return $this->container->register($provider, $c ?: $this);
    }
}
