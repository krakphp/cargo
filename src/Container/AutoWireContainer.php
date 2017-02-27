<?php

namespace Krak\Cargo\Container;

use Krak\Cargo,
    Krak\AutoArgs;

class AutoWireContainer extends AbstractContainer
{
    private $container;

    public function __construct(Cargo\Container $container, AutoArgs\AutoArgs $auto_args = null) {
        $this->container = $container;
        $this->auto_args = $auto_args ?: new AutoArgs\AutoArgs();
    }

    /** fetch from container, or if the service is a class name, we'll try to resolve it
        automatically */
    public function get($id, Cargo\Container $container = null) {
        if ($this->container->has($id) || !class_exists($id)) {
            return $this->container->get($id, $container ?: $this);
        }

        $this->add($id, null);
        return $this->get($id, $container);
    }
    public function has($id) {
        $res = $this->container->has($id);
        if ($res) {
            return $res;
        }

        return class_exists($id);
    }
    public function remove($id) {
        return $this->container->remove($id);
    }
    public function add($id, $box, array $opts = []) {
        if ($box === null && class_exists($id)) {
            $box = new Cargo\Box\AutoWireBox($this->auto_args, $id);
        }

        return $this->container->add($id, $box, $opts);
    }
    public function box($id) {
        return $this->container->box($id);
    }
    public function keys() {
        return $this->container->keys();
    }
}
