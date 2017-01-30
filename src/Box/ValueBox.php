<?php

namespace Krak\Cargo\Box;

use Krak\Cargo;

class ValueBox implements Cargo\Box
{
    private $value;

    public function __construct($value) {
        $this->value = $value;
    }

    public function unbox(Cargo\Container $container) {
        return $this->value;
    }

    public function getType() {
        return Cargo\Box::TYPE_VALUE;
    }
}
