<?php

namespace Krak\Cargo\Container;

use Krak\Cargo;

final class DetectCyclesContainer extends ContainerDecorator
{
    private $cycles = [];

    public function make($id, array $params = [], Cargo\Container $c = null) {
        if (array_key_exists($id, $this->cycles)) {
            throw new Cargo\Exception\CycleDetectedException(array_keys($this->cycles));
        }

        $this->cycles[$id] = null;
        $res = $this->container->make($id, $params, $c ?: $this);
        unset($this->cycles[$id]);
        return $res;
    }
}
