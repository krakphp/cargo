<?php

namespace Krak\CargoV2\Container;

use Krak\CargoV2;

class DetectCyclesContainer extends ContainerDecorator
{
    private $cycles = [];

    public function make($id, array $params = [], CargoV2\Container $c = null) {
        if (array_key_exists($id, $this->cycles)) {
            throw new CargoV2\Exception\CycleDetectedException(array_keys($this->cycles));
        }

        $this->cycles[$id] = null;
        $res = $this->container->make($id, $params, $c ?: $this);
        unset($this->cycles[$id]);
        return $res;
    }
}
