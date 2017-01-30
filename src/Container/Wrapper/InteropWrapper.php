<?php

namespace Krak\Cargo\Container\Wrapper;

use Krak\Cargo,
    Interop\Container;

class InteropContainer implements ContainerInterface
{
    private $container;

    public function __construct(Cargo\Container $container) {
        $this->container = $container;
    }

    public function get($id) {
        if (!$this->container->has($id)) {
            throw new Container\Exception\NotFoundException("No entry was found for '$id'");
        }

        try {
            return $this->container->get($id);
        } catch (\Exception $e) {
            throw new Container\Exception\ContainerException($e->getMessage());
        }
    }

    public function has($id) {
        return $this->container->has($id);
    }
}
