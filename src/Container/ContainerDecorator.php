<?php

namespace Krak\Cargo\Container;

use Krak\Cargo;

abstract class ContainerDecorator extends AbstractContainer
{
    protected $container;

    public function __construct(Cargo\Container $container) {
        $this->container = $container;
    }

    public function get($id, Cargo\Container $container = null) {
        return $this->container->get($id, $container ?: $this);
    }
    public function has($id) {
        return $this->container->has($id);
    }
    public function remove($id) {
        return $this->container->remove($id);
    }
    public function add($id, $box, array $opts = []) {
        return $this->container->add($id, $box, $opts);
    }
    public function box($id) {
        return $this->container->box($id);
    }
    public function keys() {
        return $this->container->keys();
    }
}
