<?php

use Krak\Cargo;

describe('Detect Cycles Container', function() {
    it('can detect cycles', function() {
        $c = new Cargo\Container\BoxContainer();
        $c = new Cargo\Container\DetectCyclesContainer($c);
        $c->add('a', function($c) {
            return $c->get('b');
        });
        Cargo\alias($c, 'a', 'c');
        Cargo\alias($c, 'c', 'b');

        try {
            $c->get('a');
            assert(false);
        } catch(Cargo\Exception\CycleDetectedException $e) {
            assert($e->getCycle() == ['a', 'b', 'c', 'a']);
        }
    });
});
