<?php

namespace Krak\Cargo\Container;

function optsFactory(array $opts) {
    return isset($opts['factory']) && $opts['factory'] == true;
}

/** Returns the wrapped box */
function optsWrapped(array $opts) {
    return isset($opts['wrapped'])
        ? $opts['wrapped']
        : null;
}

/** finds the unwrapped value */
function unwrapBox(array $box) {
    while (isset($box[1]['wrapped'])) {
        $box = $box[1]['wrapped'];
    }
    return $box;
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

function optsValue(array $opts) {
    return isset($opts['service']) && $opts['service'] == false;
}
