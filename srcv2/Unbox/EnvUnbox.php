<?php

namespace Krak\CargoV2\Unbox;

use Krak\CargoV2;

class EnvUnbox implements CargoV2\Unbox
{
    private $unbox;
    private $getenv;

    public function __construct(CargoV2\Unbox $unbox, $getenv = 'getenv') {
        $this->unbox = $unbox;
        $this->getenv = $getenv;
    }

    public function unbox($box, CargoV2\Container $container, array $params) {
        list($value, $opts) = $box;
        if (!CargoV2\Container\optsEnv($opts)) {
            return $this->unbox->unbox($box, $container, $opts);
        }

        return call_user_func($this->getenv, $value);
    }
}
