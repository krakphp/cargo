<?php

namespace Krak\CargoV2\Container;

use Krak\Arr;
use Krak\CargoV2;
use Krak\CargoV2\Exception\NotFoundException;
use Krak\CargoV2\Exception\BoxFrozenException;

/** Stores values for the containers */
class BoxContainer extends AbstractContainer
{
    private $boxes;
    private $cached;
    private $unbox;

    public function __construct(CargoV2\Unbox $unbox = null) {
        $this->boxes = [];
        $this->cached = [];
        $this->unbox = $unbox ?: new CargoV2\Unbox\ServiceUnbox();
    }

    /**
     * @throws NotFoundException
     */
    public function make($id, array $params = [], CargoV2\Container $c = null) {
        if (!$this->has($id)) {
            throw new NotFoundException("Service $id could not be found.");
        }

        // check if we have a cached entry first
        if (array_key_exists($id, $this->cached)) {
            return $this->cached[$id];
        }

        $box = $this->boxes[$id];

        $value = $this->unbox->unbox($box, $c ?: $this, $params);
        if (!optsFactory($box[1])) {
            $this->cached[$id] = $value;
        }

        return $value;
    }

    public function has($id) {
        return array_key_exists($id, $this->boxes);
    }

    public function remove($id) {
        unset($this->boxes[$id]);
        unset($this->cached[$id]);
    }

    /**
     * @throws BoxFrozenException
     */
    public function add($id, $box, array $opts = []) {
        if (array_key_exists($id, $this->cached)) {
            throw new BoxFrozenException($id);
        }
        $this->boxes[$id] = [$box, $opts];
    }

    public function box($id) {
        if (!$this->has($id)) {
            throw new NotFoundException("Service $id could not be found.");
        }

        return $this->boxes[$id];
    }

    public function count() {
        return count($this->boxes);
    }

    public function keys() {
        return array_keys($this->boxes);
    }
}
