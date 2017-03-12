<?php

namespace Krak\Cargo\Container;

use Krak\Cargo;

class BoxFactoryContainer extends ContainerDecorator
{
    private $box_factory;

    public function __construct(Cargo\Container $container, $box_factory = null) {
        parent::__construct($container);
        $this->box_factory = $box_factory ?: Cargo\stdBoxFactory();
    }

    public function add($id, $box, array $opts = []) {
        $box_factory = $this->box_factory;
        return $this->container->add($id, $box_factory($box, $opts), $opts);
    }
}
