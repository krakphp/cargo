<?php

namespace Krak\CargoV2\Container;

use ArrayAccess;
use Krak\CargoV2;

/** Stores values for the containers */
abstract class AbstractContainer implements CargoV2\Container, ArrayAccess
{
    public function offsetGet($offset) {
        return $this->get($offset);
    }
    public function offsetSet($offset, $value) {
        return $this->add($offset, $value);
    }
    public function offsetUnset($offset) {
        return $this->remove($offset);
    }
    public function offsetExists($offset) {
        return $this->has($offset);
    }

    public function wrap($id, $wrapper) {
        return CargoV2\wrap($this, $id, $wrapper);
    }
    public function protect($id, $value) {
        return CargoV2\protect($this, $id, $value);
    }
    public function factory($id, $factory = null) {
        return CargoV2\factory($this, $id, $factory);
    }
    public function singleton($id, $factory = null) {
        return CargoV2\singleton($this, $id, $factory);
    }
    public function fill(array $values) {
        return CargoV2\fill($this, $values);
    }
    public function alias($id, ...$aliases) {
        return CargoV2\alias($this, $id, ...$aliases);
    }
    public function env($var_name, $id = null) {
        return CargoV2\env($this, $var_name, $id);
    }

    public function get($id) {
        return $this->make($id, [], $this);
    }

    public function register(CargoV2\ServiceProvider $provider) {
        $provider->register($this);
    }

    abstract public function make($id, array $params = [], CargoV2\Container $c = null);
    abstract public function remove($id);
    abstract public function add($id, $box, array $opts = []);
    abstract public function has($id);
    abstract public function box($id);
    abstract public function keys();
    abstract public function count();
}
