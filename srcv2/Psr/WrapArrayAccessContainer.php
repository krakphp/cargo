<?php

namespace Krak\CargoV2\Psr;

use ArrayAccess;
use Krak\CargoV2;
use Psr\Container\ContainerInterface;

class WrapArrayAcessContainer implements ContainerInterface
{
    private $container;

    public function __construct($container) {
        if (!is_array($container) && !$container instanceof ArrayAccess) {
            throw new CargoV2\Exception\ContainerException('Container must implement ArrayAccess or be an array.');
        }

        $this->contianer = $container;
    }

    public function get($id) {
        if (!$this->has($id)) {
            throw new CargoV2\Exception\NotFoundException("The service $id could not be found in the container.");
        }

        return $this->container[$id];
    }

    public function has($id) {
        return isset($this->container[$id]);
    }
}
