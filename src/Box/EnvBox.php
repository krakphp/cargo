<?php

namespace Krak\Cargo\Box;

use Krak\Cargo;

class EnvBox implements Cargo\Box
{
    private $var_name;
    private $getenv;

    public function __construct($var_name, callable $getenv = null) {
        $this->var_name = $var_name;
        $this->getenv = $getenv ?: 'getenv';
    }

    public function unbox(Cargo\Container $container) {
        $getenv = $this->getenv;
        return $getenv($this->var_name);
    }

    public function getType() {
        return Cargo\Box::TYPE_VALUE;
    }
}
