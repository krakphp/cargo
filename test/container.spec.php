<?php

use Krak\Cargo\{
    Container\BoxContainer,
    Container,
    Unbox,
    Exception\BoxFrozenException
};
use function Krak\Cargo\{factory, replace, define, wrap, alias, container, containerFactory, env};

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
        $c->add('a', function() {
            return new StdClass();
        });
        assert($c->get('a') === $c->get('a'));
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
    it('will remove a non-factory entry', function() {
        $entry = function() {
            return new StdClass();
        };
        $c = new BoxContainer();
        $c->add('a', $entry);
        $c->remove('a');
        factory($c, 'a', $entry);
        assert($c['a'] !== $c['a']);
    });

    it('allows array access', function() {
        $c = new BoxContainer();
        $c['a'] = 1;
        $c['b'] = 2;
        unset($c['b']);
        assert(count($c) == 1);
        assert(isset($c['a']));
        assert(!isset($c['b']));
    });

    it('prevents entries from being re-defined after they have been called', function() {
        $c = new BoxContainer();
        $c->add('a', 1);
        $c->get('a');
        try {
            $c->add('a', 2);
            assert(false);
        } catch (BoxFrozenException $e) {
            assert(true);
        }
    });
});
describe('alias', function() {
    it('aliases services', function() {
        $c = new BoxContainer();
        $c->add('a', 1);
        alias($c, 'a', 'b', 'c');
        $c->add('a', 2);
        assert($c->get('a') === $c->get('b'));
        assert($c->get('a') === $c->get('c'));
    });
    it('can remove aliased services', function() {
        $c = new BoxContainer();
        $c->add('a', 1);
        alias($c, 'a', 'b', 'c');
        $c->remove('b');
        assert(count($c) == 2);
    });
    it('shows all aliased values as keys', function() {
        $c = new BoxContainer();
        $c->add('a', 1);
        alias($c, 'a', 'b', 'c');
        assert($c->keys(), ['a', 'b', 'c']);
    });
    it('can allow aliased services to be re-defined', function() {
        $c = new BoxContainer();
        $c->add('a', 1);
        alias($c, 'a', 'b');
        $c->add('b', 2);
        expect($c->get('a'))->to->equal(1);
        expect($c->get('b'))->to->equal(2);
    });
});
describe('wrap', function() {
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
    it('can wrap aliased services', function() {
        $c = new BoxContainer();
        $c->add('a', 1);
        alias($c, 'a', 'b');
        alias($c, 'b', 'c');
        wrap($c, 'a', function($val) {
            return $val + 1;
        });
        wrap($c, 'b', function($val) {
            return $val * 2;
        });
        wrap($c, 'c', function($val) {
            return $val + 3;
        });

        assert($c->get('a') == 7);
        assert($c->get('b') == 7);
        assert($c->get('c') == 7);
    });
    it('keeps wrapped factories as factories', function() {
        $c = new BoxContainer();
        factory($c, 'a', function() {
            return new StdClass();
        });
        wrap($c, 'a', function($obj) {
            $obj->id = 1;
            return $obj;
        });
        assert($c->get('a') !== $c->get('a'));
    });
});
describe('replace', function() {
    it('can simply replace a service', function() {
        $c = new BoxContainer();
        $c->add('a', 1, ['q' => 1]);
        replace($c, 'a', 2, ['r' => 2]);
        expect($c->box('a'))->to->equal([2, ['q' => 1, 'r' => 2]]);
    });
    it('can replace an aliased service', function() {
        $c = new BoxContainer();
        $c->add('a', 1);
        alias($c, 'a', 'b');
        replace($c, 'b', 2);
        expect($c->get('b'))->to->equal($c->get('a'));
        expect($c->get('a'))->to->equal(2);
    });
    it('can replace a wrapped service', function() {
        $c = new BoxContainer();
        $c->add('a', 1);
        $c->wrap('a', function($a) {
            return $a * 2;
        });
        $c->wrap('a', function($a) {
            return $a + 1;
        });
        $c->replace('a', 2);
        expect($c->get('a'))->to->equal(5);
    });
});
describe('define', function() {
    it('will add a service if not already added', function() {
        $c = new BoxContainer();
        $c->define('a', 1);
        expect($c->get('a'))->to->equal(1);
    });
    it('will replace a service if already added', function() {
        $c = new BoxContainer();
        $c->define('a', 1);
        $c->alias('a', 'b');
        $c->define('b', 2);
        expect($c->get('a'))->to->equal(2);
    });
});
describe('env', function() {
    it('grabs values from the environment', function() {
        $c = containerFactory()->env()->create();
        $c->env('PARAM_A');
        env($c, 'PARAM_B');

        putenv('PARAM_A=1234');
        putenv('PARAM_B=4321');

        assert($c['PARAM_A'] === '1234' && $c['PARAM_B'] === '4321');
    });
});
describe('protect', function() {
    it('creates non-service values', function() {
        $c = container();
        $val = function() {};
        $c->protect('a', $val);
        assert($c->get('a') === $val);
    });
});
describe('fill', function() {
    it('assigns many values at once', function() {
        $c = container();
        $c->fill([
            'a' => 1,
            'b' => 2,
        ]);
        assert($c->keys() == ['a', 'b']);
    });
});
