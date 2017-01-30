<?php

namespace Krak\Cargo\Container;

trait ArrayAccessTrait
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
}
