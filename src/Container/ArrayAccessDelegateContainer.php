<?php

namespace Krak\Cargo\Container;

use Krak\Cargo;
use ArrayAccess;

/** forwards requests to the delegate if the container does not contain the entry */
class ArrayAccessDelegateContainer extends ContainerDecorator
{
    private $delegate;

    public function __construct(Cargo\Container $container, $delegate) {
        parent::__construct($container);
        if (!is_array($delegate) && !$delegate instanceof ArrayAccess) {
            throw new \InvalidArgumentException('Expecting either an array or ArrayAccess object for the delegate.');
        }
        $this->delegate = $delegate;
    }

    public function get($id, Cargo\Container $container = null) {
        if ($this->container->has($id)) {
            return $this->container->get($id, $container ?: $this);
        }
        if (isset($this->delegate[$id])) {
            return $this->delegate[$id];
        }

        // this will just throw an exception if it doesn't exist.
        return $this->container->get($id, $container ?: $this);
    }

    public function has($id) {
        return $this->container->has($id) || isset($this->delegate[$id]);
    }
}
