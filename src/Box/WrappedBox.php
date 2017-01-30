<?php

namespace Krak\Cargo\Box;

use Krak\Cargo;

class WrappedBox implements Cargo\Box
{
    private $box;
    private $wrapper;

    public function __construct(Cargo\Box $box, $wrapper) {
        $this->box = $box;
        $this->wrapper = $wrapper;
    }

    public function unbox(Cargo\Container $container) {
        $wrapper = $this->wrapper;
        return $wrapper($this->box->unbox($container), $container);
    }

    public function getType() {
        return $this->box->getType();
    }
}
