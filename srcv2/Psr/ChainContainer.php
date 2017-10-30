<?php

namespace Krak\CargoV2\Psr;

use Krak\CargoV2;
use Psr\Container\ContainerInterface;

class ChainContainer implements ContainerInterface
{
    private $containers;

    public function __construct(array $containers) {
        $this->containers = $containers;
    }

    public function get($id) {
        foreach ($this->containers as $c) {
            if ($c->has($id)) {
                return $c->get($id);
            }
        }

        throw new CargoV2\Exception\NotFoundException("The service $id could not be found in any of the containers.");
    }

    public function has($id) {
        foreach ($this->containers as $c) {
            if ($c->has($id)) {
                return true;
            }
        }

        return false;
    }
}
