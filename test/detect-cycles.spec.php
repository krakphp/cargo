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

        expect(function() use ($c) {
            $c->get('a');
        })->to->throw(Cargo\Exception\CycleDetectedException::class);
    });
});
