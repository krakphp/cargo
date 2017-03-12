<?php

namespace Krak\Cargo\Container;

use Krak\Cargo;

trait AuxillaryMethodsTrait
{
    public function wrap($id, $wrapper) {
        return Cargo\wrap($this, $id, $wrapper);
    }
    public function protect($id, $value) {
        return Cargo\protect($this, $id, $value);
    }
    public function factory($id, $factory = null) {
        return Cargo\factory($this, $id, $factory);
    }
    public function singleton($id, $factory = null) {
        return Cargo\singleton($this, $id, $factory);
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
    public function toPimple() {
        return Cargo\toPimple($this);
    }
    public function toInterop() {
        return Cargo\toInterop($this);
    }
    public function register(Cargo\ServiceProvider $provider, array $values = []) {
        return Cargo\register($this, $provider, $values);
    }
}
