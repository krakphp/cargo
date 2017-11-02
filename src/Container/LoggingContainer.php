<?php

namespace Krak\Cargo\Container;

use Krak\Cargo;
use Psr\Log;

class LoggingContainer extends ContainerDecorator
{
    private $logger;
    private $level;

    public function __construct(Cargo\Container $container, Log\LoggerInterface $logger, $level = Log\LogLevel::DEBUG) {
        parent::__construct($container);
        $this->logger = $logger;
        $this->level = $level;
    }

    public function make($id, array $params = [], Cargo\Container $c = null) {
        $this->logger->log($this->level, "Cargo Container making service {id}", [
            'id' => $id,
            'params' => $params,
        ]);

        return $this->container->make($id, $params, $c ?: $this);
    }
    public function remove($id) {
        $this->logger->log($this->level, "Cargo Container removing service {id}", [
            'id' => $id
        ]);
        return $this->container->remove($id);
    }
    public function add($id, $box, array $opts = []) {
        $this->logger->log($this->level, "Cargo Container adding service {id}", [
            'id' => $id,
            'box' => $box,
            'opts' => $opts,
        ]);
        return $this->container->add($id, $box, $opts);
    }
    public function has($id) {
        $this->logger->log($this->level, "Cargo Container checking if service {id} exists", [
            'id' => $id,
        ]);
        return $this->container->has($id);
    }
    public function box($id) {
        $this->logger->log($this->level, "Cargo Container retrieving box {id}", [
            'id' => $id,
        ]);
        return $this->container->box($id);
    }
    public function register(Cargo\ServiceProvider $provider, Cargo\Container $c = null) {
        $this->logger->log($this->level, "Cargo Container registering service provider {provider}", [
            'provider' => get_class($provider),
        ]);
        return $this->container->register($provider, $c ?: $this);
    }
}
