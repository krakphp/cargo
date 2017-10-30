<?php

namespace Krak\CargoV2\Unbox;

use Closure;
use Krak\CargoV2;

class ServiceUnbox implements CargoV2\Unbox
{
    public function unbox($box, CargoV2\Container $container, array $params) {
        list($value, $opts) = $box;

        if (CargoV2\Container\optsAlias($opts)) {
            return $container->make($value, $params);
        }

        if (!$value instanceof Closure) {
            return $value;
        }

        $wrapped = CargoV2\Container\optsWrapped($opts);
        if ($wrapped) {
            $unboxed_value = $this->unbox($wrapped, $container, $params);
            return $value($unboxed_value, $container, $params);
        }

        return $value($container, $params);
    }
}
