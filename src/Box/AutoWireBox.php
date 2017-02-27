<?php

namespace Krak\Cargo\Box;

use Krak\Cargo;

class AutoWireBox implements Cargo\Box
{
    private $class_name;
    private $auto_args;

    public function __construct($auto_args, $class_name) {
        $this->auto_args = $auto_args;
        $this->class_name = $class_name;
    }

    public function unbox(Cargo\Container $c) {
        return $this->auto_args->construct($this->class_name, [
            'objects' => [$c],
            'container' => Cargo\toInterop($c),
        ]);
    }

    public function getType() {
        return Cargo\Box::TYPE_SERVICE;
    }
}
