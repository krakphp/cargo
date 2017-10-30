<?php

namespace Krak\CargoV2\Container;

function optsFactory(array $opts) {
    return isset($opts['factory']) && $opts['factory'] == true;
}

/** Returns the wrapped box */
function optsWrapped(array $opts) {
    return isset($opts['wrapped'])
        ? $opts['wrapped']
        : null;
}

function optsEnv(array $opts) {
    return isset($opts['env']) && $opts['env'] == true;
}

function optsAlias(array $opts) {
    return isset($opts['alias']) && $opts['alias'] == true;
}

function optsService(array $opts) {
    return isset($opts['service']) && $opts['service'] == true;
}
