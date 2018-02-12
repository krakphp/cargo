<?php

namespace Krak\Cargo;

use Psr\Log;

class ContainerFactory
{
    private $env = false;
    private $auto_wire = null;
    private $alias = false;
    private $detect_cycles = false;
    private $logger = null;
    private $log_level = null;
    private $lazy = null;

    public function autoWire($val = true) {
        $this->auto_wire = $val;
        return $this;
    }

    public function alias($val = true) {
        $this->alias = $val;
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

    public function log(Log\LoggerInterface $logger = null, $level = Log\LogLevel::DEBUG) {
        $this->logger = $logger;
        $this->log_level = $level;
        return $this;
    }

    public function lazy($lazy_config) {
        $this->lazy = $lazy_config;
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
        if ($this->alias) {
            $container = new Container\AliasContainer($container);
        }
        if ($this->logger) {
            $container = new Container\LoggingContainer($container, $this->logger, $this->log_level);
        }
        if ($this->detect_cycles) {
            $container = new Container\DetectCyclesContainer($container);
        }
        if ($this->lazy) {
            $container = is_string($this->lazy)
                ? Container\LazyRegisterContainer::createFromCacheFile($container, $this->lazy)
                : new Container\LazyRegisterContainer($container, $this->lazy);
        }

        return $container;
    }
}
