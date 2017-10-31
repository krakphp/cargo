<?php

use Krak\Cargo;

describe('Psr Chain Container', function() {
    it('chains containers together', function() {
        $c1 = Cargo\container(['a' => 1]);
        $c2 = Cargo\container(['b' => 2]);
        $c = new Cargo\Psr\ChainContainer([$c1, $c2]);
        assert($c->get('a') == 1);
        assert($c->get('b') == 2);
    });
});
describe('Psr Wrap Array Access Container', function() {
    it('wraps any array accessable value into a container interface', function() {
        $c = new Cargo\Psr\WrapArrayAccessContainer(['a' => 1]);
        assert($c->get('a') == 1);
    });
});
