<?php

namespace Krak\Cargo\Container;

use ArrayAccess;
use Krak\Cargo;

/** Stores values for the containers */
abstract class AbstractContainer implements Cargo\Container, ArrayAccess
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
        return Cargo\wrap($this, $id, $wrapper);
    }
    public function replace($id, $value, array $opts = []) {
        return Cargo\replace($this, $id, $value, $opts);
    }
    public function define($id, $value, array $opts = []) {
        return Cargo\define($this, $id, $value, $opts);
    }
    public function protect($id, $value) {
        return Cargo\protect($this, $id, $value);
    }
    public function factory($id, $factory = null) {
        return Cargo\factory($this, $id, $factory);
    }
    public function singleton($id, $service = null) {
        return Cargo\singleton($this, $id, $service);
    }
    public function fill(array $values) {
        return Cargo\fill($this, $values);
    }
    public function alias($id, ...$aliases) {
        return Cargo\alias($this, $id, ...$aliases);
    }
    public function env($var_name, $id = null) {
        return Cargo\env($this, $var_name, $id);
    }

    public function get($id) {
        return $this->make($id, [], $this);
    }

    public function register(Cargo\ServiceProvider $provider, Cargo\Container $c = null) {
        $provider->register($c ?: $this);
    }

    public function count() {
        return count($this->keys());
    }

    abstract public function make($id, array $params = [], Cargo\Container $c = null);
    abstract public function remove($id);
    abstract public function add($id, $box, array $opts = []);
    abstract public function has($id);
    abstract public function box($id);
    abstract public function keys();
}
