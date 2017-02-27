<?php

namespace Krak\Cargo\Container;

use Krak\Cargo;

class BoxContainer extends AbstractContainer
{
    private $boxes;
    private $aliases;

    public function __construct() {
        $this->boxes = [];
        $this->aliases = [];
    }

    public function get($id, Cargo\Container $container = null) {
        if (!$this->has($id)) {
            throw new Cargo\Exception\NotFoundException("Service '$id' was not previously defined.");
        }

        return $this->boxes[$id]->unbox($container ?: $this);
    }
    public function has($id) {
        return array_key_exists($id, $this->boxes);
    }
    public function remove($id) {
        unset($this->boxes[$id]);
    }
    public function add($id, $box, array $opts = []) {
        if (!$box instanceof Cargo\Box) {
            throw new Cargo\Exception\ContainerException('$box must be an instance of Krak\Cargo\Box');
        }
        $this->boxes[$id] = $box;
    }
    /** get the box for this service */
    public function box($id) {
        if (!$this->has($id)) {
            throw new Cargo\Exception\NotFoundException("Box '$id' was not defined.");
        }

        return $this->boxes[$id];
    }

    public function keys() {
        return array_keys($this->boxes);
    }
}
