<?php

namespace Krak\Cargo;

interface Unbox
{
    public function unbox($box, Container $container, array $params);
}
