<?php

namespace Krak\Cargo\Container;

use Krak\Cargo;

/** Freezes a service definition after it's been used preventing an override of that
    service */
class FreezingContainer extends ContainerDecorator
{
    private $only_services;
    private $frozen;

    public function __construct(Cargo\Container $container, $only_services = true) {
        parent::__construct($container);
        $this->only_services = $only_services;
        $this->frozen = [];
    }

    public function get($id, Cargo\Container $container = null) {
        $should_freeze = !$this->only_services ||
            ($this->box($id) && $this->box($id)->getType() == Cargo\Box::TYPE_SERVICE);

        if ($should_freeze) {
            $this->frozen[$id] = null;
        }

        return $this->container->get($id, $container ?: $this);
    }
    public function add($id, $box, array $opts = []) {
        if (array_key_exists($id, $this->frozen)) {
            throw new Cargo\Exception\ContainerException("Cannot redefine already frozen service '$id'");
        }

        return $this->container->add($id, $box, $opts);
    }
}
