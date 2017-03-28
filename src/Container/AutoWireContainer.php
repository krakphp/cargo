<?php

namespace Krak\Cargo\Container;

use Krak\Cargo,
    Krak\AutoArgs;

class AutoWireContainer extends ContainerDecorator
{
    private $auto_args;

    public function __construct(Cargo\Container $container, AutoArgs\AutoArgs $auto_args = null) {
        parent::__construct($container);
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
    public function add($id, $box, array $opts = []) {
        if ($box === null && class_exists($id)) {
            $box = new Cargo\Box\AutoWireBox($this->auto_args, $id);
        } else if ($box && is_string($box) && class_exists($box) && isset($opts['singleton'])) {
            $box = new Cargo\Box\AutoWireBox($this->auto_args, $box);
        }

        return $this->container->add($id, $box, $opts);
    }
}
