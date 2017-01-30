<?php

namespace Krak\Cargo;

interface Container extends \ArrayAccess
{
    public function get($id, Container $container = null);
    public function has($id);
    public function remove($id);
    public function add($id, $box = null);
    /** get the box for this service */
    public function box($id);
    /** returns all of the keys defined in the container */
    public function keys();
}
