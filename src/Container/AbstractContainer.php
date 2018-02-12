<?php

namespace Krak\Cargo\Container;

use ArrayAccess;
use Krak\Cargo;

/** Stores values for the containers */
abstract class AbstractContainer implements Cargo\Container, ArrayAccess
{
    private static $methods = [];

    public static function registerContainerMethods(array $methods) {
        self::$methods = array_merge(self::$methods, $methods);
    }

    public function __call($method, array $args) {
        if (!isset(self::$methods[$method])) {
            throw new \BadMethodCallException('Invalid method: ' . $method);
        }

        return self::$methods[$method]($this, ...$args);
    }

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
