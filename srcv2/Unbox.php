<?php

namespace Krak\CargoV2;

interface Unbox
{
    public function unbox($box, Container $container, array $params);
}
