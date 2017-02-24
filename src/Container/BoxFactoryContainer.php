<?php

namespace Krak\Cargo\Container;

use Krak\Cargo;

class BoxFactoryContainer extends AbstractContainer
{
    private $container;
    private $box_factory;

    public function __construct(Cargo\Container $container, $box_factory = null) {
        $this->container = $container;
        $this->box_factory = $box_factory ?: Cargo\stdBoxFactory();
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
        $box_factory = $this->box_factory;
        return $this->container->add($id, $box_factory($box, $opts), $opts);
    }
    public function box($id) {
        return $this->container->box($id);
    }
    public function keys() {
        return $this->container->keys();
    }
}
