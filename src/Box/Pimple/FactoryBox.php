<?php

namespace Krak\Cargo\Box\Pimple;

use Krak\Cargo,
    Pimple;

class FactoryBox implements Cargo\Box
{
    private $factory;
    private $pimple;

    public function __construct($factory, Pimple\Container $pimple) {
        $this->factory = $factory;
        $this->pimple = $pimple;
    }

    public function unbox(Cargo\Container $container) {
        $factory = $this->factory;
        return $factory($this->pimple);
    }

    public function getType() {
        return Cargo\Box::TYPE_SERVICE;
    }
}
