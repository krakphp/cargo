<?php

namespace Krak\Cargo\Box\Pimple;

use Krak\Cargo,
    Pimple;

class ExtendBox implements Cargo\Box
{
    private $box;
    private $wrapper;
    private $pimple;

    public function __construct(Cargo\Box $box, $wrapper, Pimple\Container $pimple) {
        $this->box = $box;
        $this->wrapper = $wrapper;
        $this->pimple = $pimple;
    }

    public function unbox(Cargo\Container $container) {
        $wrapper = $this->wrapper;
        return $wrapper($this->box->unbox($container), $this->pimple);
    }

    public function getType() {
        return $this->box->getType();
    }
}
