<?php

namespace Krak\Cargo\Container;

trait CountableTrait
{
    public function count() {
        return count($this->keys());
    }
}
