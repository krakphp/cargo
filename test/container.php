<?php

use Krak\Cargo;

describe('alias', function() {
    it('aliases a box', function() {
        $c = Cargo\container();
        $c['a'] = function() {};
        Cargo\alias($c, 'a', 'b');
        $c->alias('b', 'c');
        assert($c->box('a') === $c->box('b') && $c->box('b') === $c->box('c'));
    });
});
