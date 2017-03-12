<?php

namespace Krak\Cargo\Container;

use Krak\Cargo;

abstract class AbstractContainer implements Cargo\Container
{
    use ArrayAccessTrait;
    use CountableTrait;
    use AuxillaryMethodsTrait;

    abstract public function get($id, Cargo\Container $container = null);
    abstract public function has($id);
    abstract public function remove($id);
    abstract public function add($id, $box, array $opts = []);
    /** get the box for this service */
    abstract public function box($id);
    abstract public function keys();
}
