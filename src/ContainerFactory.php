<?php

namespace Krak\Cargo;

class ContainerFactory
{
    private $env = false;
    private $auto_wire = null;
    private $detect_cycles = false;

    public function autoWire($val = true) {
        $this->auto_wire = $val;
        return $this;
    }

    public function env($val = true) {
        $this->env = $val;
        return $this;
    }

    public function detectCycles($val = true) {
        $this->detect_cycles = $val;
        return $this;
    }

    public function create() {
        $unbox = new Unbox\ServiceUnbox();
        if ($this->auto_wire === null) {
            $this->auto_wire = class_exists('Krak\\AutoArgs\\AutoArgs');
        }

        if ($this->env) {
            $unbox = new Unbox\EnvUnbox($unbox);
        }
        if ($this->auto_wire) {
            $unbox = new Unbox\AutoWireUnbox($unbox);
        }
        $container = new Container\BoxContainer($unbox);
        if ($this->auto_wire) {
            $container = new Container\AutoWireContainer($container);
        }
        if ($this->detect_cycles) {
            $container = new Container\DetectCyclesContainer($container);
        }

        return $container;
    }
}
