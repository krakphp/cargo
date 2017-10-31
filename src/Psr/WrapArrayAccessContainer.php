<?php

namespace Krak\Cargo\Psr;

use ArrayAccess;
use Krak\Cargo;
use Psr\Container\ContainerInterface;

class WrapArrayAcessContainer implements ContainerInterface
{
    private $container;

    public function __construct($container) {
        if (!is_array($container) && !$container instanceof ArrayAccess) {
            throw new Cargo\Exception\ContainerException('Container must implement ArrayAccess or be an array.');
        }

        $this->contianer = $container;
    }

    public function get($id) {
        if (!$this->has($id)) {
            throw new Cargo\Exception\NotFoundException("The service $id could not be found in the container.");
        }

        return $this->container[$id];
    }

    public function has($id) {
        return isset($this->container[$id]);
    }
}
