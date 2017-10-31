<?php

namespace Krak\Cargo;

use Psr\Container\ContainerInterface;
use Countable;

interface Container extends ContainerInterface, Countable
{
    public function make($id, array $params = [], Container $c = null);
    public function remove($id);
    public function add($id, $box, array $opts = []);
    public function keys();
    public function box($id);
    public function register(ServiceProvider $provider);
}
