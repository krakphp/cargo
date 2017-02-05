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
describe('env', function() {
    it('grabs values from the environment', function() {
        $c = Cargo\container();
        $c->env('PARAM_A');
        Cargo\env($c, 'PARAM_B');

        putenv('PARAM_A=1234');
        putenv('PARAM_B=4321');

        assert($c['PARAM_A'] === '1234' && $c['PARAM_B'] === '4321');
    });
});
