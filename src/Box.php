<?php

namespace Krak\Cargo;

/** Value wrapper designed to leave the implementation of how it's invoked to the invoker */
interface Box {
    const TYPE_VALUE = 'value';
    const TYPE_SERVICE = 'service';

    public function unbox(Container $container);
    public function getType();
}
