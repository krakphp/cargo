<?php

namespace Krak\CargoV2\Unbox;

use Krak\AutoArgs\AutoArgs;
use Krak\CargoV2;
use Exception;

class AutoWireUnbox implements CargoV2\Unbox
{
    private $unbox;
    private $auto_args;

    public function __construct(CargoV2\Unbox $unbox, AutoArgs $auto_args = null) {
        $this->unbox = $unbox;
        $this->auto_args = $auto_args ?: new AutoArgs();
    }

    public function unbox($box, CargoV2\Container $container, array $params) {
        list($value, $opts) = $box;

        $should_autowire = is_string($value) && CargoV2\Container\optsService($opts);
        if (!$should_autowire) {
            return $this->unbox->unbox($box, $container, $params);
        }

        $ctx = [
            'container' => $container,
            'objects' => [$container],
            'vars' => $params,
        ];

        if (!class_exists($value) && !function_exists($value)) {
            throw new CargoV2\Exception\AutoWireException("Could not automatically resolve service $value because it is neither a class or function.");
        }

        $just_thrown = false;
        try {
            if (class_exists($value)) {
                $value = $this->auto_args->construct($value, $ctx);
            } else {
                $value = $this->auto_args->invoke($value, $ctx);
            }
        } catch (Exception $e) {
            throw new CargoV2\Exception\AutoWireException("Could not automatically resolve service $value", 0, $e);
        }

        return $value;
    }
}
