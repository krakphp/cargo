<?php

namespace Krak\Cargo\Container;

use Krak\Cargo;

abstract class AbstractContainer implements Cargo\Container
{
    use ArrayAccessTrait;

    public function wrap($id, $wrapper) {
        return Cargo\wrap($this, $id, $wrapper);
    }
    public function protect($id, $value) {
        return Cargo\protect($id, $value);
    }
    public function factory($id, $factory) {
        return Cargo\factory($this, $id, $factory);
    }
    public function singleton($id, $factory) {
        return Cargo\singleton($this, $id, $factory);
    }
    public function fill(array $values) {
        return Cargo\fill($this, $values);
    }
    public function alias($id, ...$aliases) {
        return Cargo\alias($this, $id, ...$aliases);
    }

    public function toPimple() {
        return Cargo\toPimple($this);
    }

    public function toInterop() {
        return Cargo\toInterop($this);
    }

    public function register(Cargo\ServiceProvider $provider, array $values = []) {
        return Cargo\register($this, $provider, $values);
    }

    abstract public function get($id, Cargo\Container $container = null);
    abstract public function has($id);
    abstract public function remove($id);
    abstract public function add($id, $box = null);
    /** get the box for this service */
    abstract public function box($id);
    abstract public function keys();
}
