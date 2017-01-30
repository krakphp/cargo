<?php

namespace Krak\Cargo\Box;

use Krak\Cargo;

class CachedBox implements Cargo\Box
{
    private $box;
    private $cached;

    public function __construct(Cargo\Box $box) {
        $this->box = $box;
    }

    public function unbox(Cargo\Container $container) {
        if ($this->cached) {
            return $this->cached;
        }

        $this->cached = $this->box->unbox($container);
        return $this->cached;
    }

    public function getType() {
        return $this->box->getType();
    }
}
