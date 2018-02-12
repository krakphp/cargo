<?php

namespace Krak\Cargo\Container;

use Krak\AutoArgs;
use Krak\Cargo;

final class AutoWireContainer extends ContainerDecorator
{
    public function make($id, array $params = [], Cargo\Container $c = null) {
        if ($this->container->has($id)) {
            return $this->container->make($id, $params, $c ?: $this);
        }

        if ($this->has($id)) {
            Cargo\singleton($this, $id);
        }

        return $this->container->make($id, $params, $c ?: $this);
    }

    public function has($id) {
        return $this->container->has($id)
            || class_exists($id)
            || function_exists($id);
    }
}
