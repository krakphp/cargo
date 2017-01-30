<?php

namespace Krak\Cargo;

interface ServiceProvider
{
    public function register(Container $container);
}
