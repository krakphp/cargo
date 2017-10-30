<?php

namespace Krak\CargoV2\Exception;

class CycleDetectedException extends ContainerException
{
    private $cycle;

    public function __construct(array $cycle) {
        $cycle[] = $cycle[0];
        $this->cycle = $cycle;
        parent::__construct("Cycle detected while trying to resolve {$cycle[0]}: " . implode(' -> ', $cycle));
    }

    public function getCycle() {
        return $this->cycle;
    }
}
