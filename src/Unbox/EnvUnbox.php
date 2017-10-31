<?php

namespace Krak\Cargo\Unbox;

use Krak\Cargo;

class EnvUnbox implements Cargo\Unbox
{
    private $unbox;
    private $getenv;

    public function __construct(Cargo\Unbox $unbox, $getenv = 'getenv') {
        $this->unbox = $unbox;
        $this->getenv = $getenv;
    }

    public function unbox($box, Cargo\Container $container, array $params) {
        list($value, $opts) = $box;
        if (!Cargo\Container\optsEnv($opts)) {
            return $this->unbox->unbox($box, $container, $opts);
        }

        return call_user_func($this->getenv, $value);
    }
}
