<?php

namespace Krak\Cargo\Container\Wrapper;

use Krak\Cargo,
    Psr\Container;

class InteropWrapper implements Container\ContainerInterface
{
    private $container;

    public function __construct(Cargo\Container $container) {
        $this->container = $container;
    }

    public function get($id) {
        return $this->container->get($id);
    }

    public function has($id) {
        return $this->container->has($id);
    }
}
