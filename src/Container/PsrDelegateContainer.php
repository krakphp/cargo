<?php

namespace Krak\Cargo\Container;

use Krak\Cargo;
use Psr\Container\ContainerInterface;

/** forwards requests to the delegate if the container does not contain the entry */
class PsrDelegateContainer extends ContainerDecorator
{
    private $delegate;

    public function __construct(Cargo\Container $container, ContainerInterface $delegate) {
        parent::__construct($container);
        $this->delegate = $delegate;
    }

    public function get($id, Cargo\Container $container = null) {
        if ($this->container->has($id)) {
            return $this->container->get($id, $container ?: $this);
        }
        if ($this->delegate->has($id)) {
            return $this->delegate->get($id);
        }

        // this will just throw an exception if it doesn't exist.
        return $this->container->get($id, $container ?: $this);
    }

    public function has($id) {
        return $this->container->has($id) || $this->delegate->has($id);
    }
}
