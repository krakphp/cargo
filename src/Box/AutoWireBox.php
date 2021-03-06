<?php

namespace Krak\Cargo\Box;

use Krak\Cargo;
use Krak\AutoArgs;
use Exception;

class AutoWireBox implements Cargo\Box
{
    private $class_name;
    private $auto_args;

    public function __construct(AutoArgs\AutoArgs $auto_args, $class_name) {
        $this->auto_args = $auto_args;
        $this->class_name = $class_name;
    }

    public function unbox(Cargo\Container $c) {
        try {
            return $this->auto_args->construct($this->class_name, [
                'objects' => [$c],
                'container' => Cargo\toInterop($c),
            ]);
        } catch (Exception $e) {
            throw new Cargo\Exception\ContainerException("Could not automatically resolve service '$this->class_name'", 0, $e);
        }
    }

    public function getType() {
        return Cargo\Box::TYPE_SERVICE;
    }
}
