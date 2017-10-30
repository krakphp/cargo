<?php

namespace Krak\CargoV2\Container;

use Krak\AutoArgs;
use Krak\CargoV2;

class AutoWireContainer extends ContainerDecorator
{
    public function make($id, array $params = [], CargoV2\Container $c = null) {
        if ($this->container->has($id)) {
            return $this->container->make($id, $params, $c ?: $this);
        }

        if ($this->has($id)) {
            CargoV2\singleton($this, $id);
        }

        return $this->container->make($id, $params, $c ?: $this);
    }

    public function has($id) {
        return $this->container->has($id)
            || class_exists($id)
            || function_exists($id);
    }
}
