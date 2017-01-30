<?php

namespace Krak\Cargo\Container;

use Krak\Cargo;

class BoxContainer extends AbstractContainer
{
    private $boxes;
    private $box_factory;

    public function __construct($box_factory = null) {
        $this->boxes = [];
        $this->box_factory = $box_factory ?: Cargo\cachedBoxFactory();
    }

    public function get($id, Cargo\Container $container = null) {
        if (!$this->has($id)) {
            throw new \RuntimeException("Service '$id' was not previously defined.");
        }

        return $this->boxes[$id]->unbox($container ?: $this);
    }
    public function has($id) {
        return array_key_exists($id, $this->boxes);
    }
    public function remove($id) {
        unset($this->boxes[$id]);
    }
    public function add($id, $box = null) {
        $box_factory = $this->box_factory;
        $this->boxes[$id] = $box_factory($box);
    }
    /** get the box for this service */
    public function box($id) {
        if (!$this->has($id)) {
            throw new \RuntimeException("Box '$id' was not defined.");
        }

        return $this->boxes[$id];
    }

    public function keys() {
        return array_keys($this->boxes);
    }
}
