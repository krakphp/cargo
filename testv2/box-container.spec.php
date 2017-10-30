<?php

use Krak\Cargo\{
    Container\BoxContainer,
    Container,
    Unbox,
    Exception\BoxFrozenException
};
use function Krak\Cargo\{factory, wrap, alias};

describe('Box Container', function() {
    it('can add boxes', function() {
        $c = new BoxContainer();
        $c->add('a', 'value');
        $c->add('b', 'value');
        assert(count($c) == 2);
    });
    it('can remove boxes', function() {
        $c = new BoxContainer();
        $c->add('a', 'value');
        $c->add('b', 'value');
        $c->remove('b');
        assert(count($c) == 1);
    });
    it('retrieve the stored keys', function() {
        $c = new BoxContainer();
        $c->add('a', 'value');
        $c->add('b', 'value');
        assert($c->keys() == ['a', 'b']);
    });
    it('can retrieve stored values', function() {
        $c = new BoxContainer();
        $c->add('a', 'value');
        assert($c->get('a') == 'value');
    });
    it('caches retrieved values', function() {
        $c = new BoxContainer();
        $c->add('a', 'value');
        $c->add('a', 'value2');
        $c->get('a');
        try {
            $c->add('a', 'value3');
        } catch (BoxFrozenException $e) {}

        assert($c->get('a') == 'value2');
    });
    it('unboxes services', function() {
        $c = new BoxContainer(new class implements Unbox {
            public function unbox($box, Container $c, array $opts = []) {
                return $box[0] * 2;
            }
        });

        $c->add('a', 2);
        assert($c->get('a') == 4);
    });
    it('does not cache factory boxes', function() {
        $c = new BoxContainer();
        factory($c, 'a', function() {
            return new StdClass();
        });

        assert($c->get('a') !== $c->get('a'));
    });
    it('wraps services', function() {
        $c = new BoxContainer();
        $c->add('a', 1);
        wrap($c, 'a', function($val) {
            return $val + 1;
        });
        wrap($c, 'a', function($val) {
            return $val * 2;
        });
        assert($c->get('a') == 4);
    });
    it('aliases services', function() {
        $c = new BoxContainer();
        $c->add('a', 1);
        alias($c, 'a', 'b', 'c');
        $c->add('a', 2);
        assert($c->get('a') === $c->get('b'));
        assert($c->get('a') === $c->get('c'));
    });
});
