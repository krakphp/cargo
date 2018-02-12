<?php

namespace Krak\Cargo\Container;

use Krak\Cargo;
use Psr\Log;

final class LoggingContainer extends ContainerDecorator
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
    public function add($id, $value, array $opts = []) {
        $this->logger->log($this->level, "Cargo Container adding service {id}", [
            'id' => $id,
            'value' => $value,
            'opts' => $opts,
        ]);
        return $this->container->add($id, $value, $opts);
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
