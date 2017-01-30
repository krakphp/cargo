<?php

namespace Krak\Cargo\Box;

use Krak\Cargo;

class LazyBox implements Cargo\Box
{
    private $factory;

    public function __construct($factory) {
        $this->factory = $factory;
    }

    public function unbox(Cargo\Container $container) {
        $factory = $this->factory;
        return $factory($container);
    }

    public function getType() {
        return Cargo\Box::TYPE_SERVICE;
    }
}
