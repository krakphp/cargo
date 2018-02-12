<?php

namespace Krak\Cargo\Unbox;

use Closure;
use Krak\Cargo;

class ServiceUnbox implements Cargo\Unbox
{
    public function unbox($box, Cargo\Container $container, array $params) {
        list($value, $opts) = $box;

        // if (Cargo\Container\optsAlias($opts)) {
        //     return $container->make($value, $params);
        // }

        if (Cargo\Container\optsValue($opts) || !$value instanceof Closure) {
            return $value;
        }

        $wrapped = Cargo\Container\optsWrapped($opts);
        if ($wrapped) {
            $unboxed_value = $this->unbox($wrapped, $container, $params);
            return $value($unboxed_value, $container, $params);
        }

        return $value($container, $params);
    }
}
