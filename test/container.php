<?php

use Krak\Cargo;
use Psr\Container\ContainerInterface;

class ServiceA {
    public $c;
    private $psr;
    public function __construct(Cargo\Container $c, ContainerInterface $psr) {
        $this->c = $c;
        $this->psr = $psr;
    }
}

describe('Container', function() {
    it('creates service definitions with top-level container being passed in', function() {
        $c = Cargo\container();
        $c['a'] = function($container) use ($c) {
            assert($c === $container);
            return 1;
        };
        $c['a'];
    });
});
describe('CountableTrait', function() {
    it('can count the values in the container from the keys', function() {
        $c = new Cargo\Container\BoxFactoryContainer(
            new Cargo\Container\BoxContainer()
        );
        $c->add('a', 1);
        $c->add('b', 2);
        assert(count($c) == 2);
    });
});
describe('ArrayAccessTrait', function() {
    beforeEach(function() {
        $this->c = new Cargo\Container\BoxFactoryContainer(
            new Cargo\Container\BoxContainer()
        );
    });
    it('can add values', function() {
        $this->c['a'] = 1;
        assert($this->c->has('a'));
    });
    it('can get values', function() {
        $this->c->add('a', 1);
        assert($this->c['a'] == 1);
    });
    it('can check if values exist', function() {
        $this->c->add('a', 1);
        assert(isset($this->c['a']) && !isset($this->c['b']));
    });
    it('can remove a value', function() {
        $this->c->add('a', 1);
        unset($this->c['a']);
        assert(count($this->c) == 0);
    });
});
describe('AliasContainer', function() {
    beforeEach(function() {
        $this->c = new Cargo\Container\AliasContainer(
            new Cargo\Container\BoxFactoryContainer(
                new Cargo\Container\BoxContainer()
            )
        );
        $this->c->add('a', 1);
        $this->c->alias('a', 'b');
    });
    it('aliases the get', function() {
        assert($this->c->get('a') === $this->c->get('b'));
    });
    it('aliases the has', function() {
        assert($this->c->has('a') === $this->c->has('b'));
    });
    it('aliases the box', function() {
        assert($this->c->box('a') === $this->c->box('b'));
    });
    it('shows all of the aliases keys', function() {
        assert($this->c->keys() == ['a', 'b']);
    });
    it('can remove an alias', function() {
        $this->c->remove('b');
        assert(count($this->c) == 1);
    });
    it('can remove the aliased entries', function() {
        $this->c->remove('a');
        assert(count($this->c) == 0);
    });
    it('allows you to wrap aliased services', function() {
        $this->c->wrap('b', function() {
            return 2;
        });
        assert($this->c['a'] == $this->c['b'] && $this->c['a'] == 2);
    });
});
describe('SingletonContainer', function() {
    beforeEach(function() {
        $c = new Cargo\Container\BoxContainer();
        $c = new Cargo\Container\SingletonContainer($c);
        $c = new Cargo\Container\BoxFactoryContainer($c);
        $c = new Cargo\Container\AliasContainer($c);
        $this->c = $c;
    });
    it('will default cache services', function() {
        $this->c['a'] = function() {
            static $i = 0;
            return $i++;
        };
        assert($this->c['a'] === $this->c['a']);
    });
    it('throws exception if service returns null', function() {
        $this->c['a'] = function() {};
        try {
            $this->c['a'];
            assert(false);
        } catch (Cargo\Exception\ContainerException $e) {
            assert(true);
        }
    });
    it('will not cache a factory service or value', function() {
        $this->c['a'] = 1;
        $this->c['a'] = 2;
        $this->c->factory('b', function() {
            static $i = 0;
            return $i++;
        });
        assert($this->c['a'] == 2 && $this->c['b'] == 0 && $this->c['b'] == 1);
    });
    it('will remove cached entry', function() {
        $entry = function() {
            static $i = 0;
            return $i++;
        };
        $this->c['a'] = $entry;
        $this->c->remove('a');
        $this->c->factory('a', $entry);
        assert($this->c['a'] != $this->c['a']);
    });
    it('will cache wrapped boxes if the original box was cached', function() {
        $this->c['a'] = function() {
            return 1;
        };
        $called = 0;
        $this->c->wrap('a', function($val) use (&$called) {
            $called += 1;
            return $val + 1;
        });
        assert($this->c['a'] == $this->c['a'] && $called == 1);
    });
    it('will not cache wrapped boxes if it was a factory service', function() {
        $this->c->factory('a', function() {
            return 1;
        });
        $called = 0;
        $this->c->wrap('a', function($val) use (&$called) {
            $called += 1;
            return $val + 1;
        });
        $this->c['a'];
        $this->c['a'];
        $this->c['a'];
        assert($called == 3);
    });
    it('will work well with aliased services', function() {
        $this->c['a'] = function() {
            static $i = 0;
            return $i++;
        };
        $this->c->factory('b', function() {
            static $i = 0;
            return $i++;
        });
        $this->c->alias('a', 'a_');
        $this->c->alias('b', 'b_');

        assert($this->c['a_'] == $this->c['a_'] && $this->c['b_'] != $this->c['b_']);
    });
});
describe('AutoWireContainer', function() {
    beforeEach(function() {
        $c = new Cargo\Container\BoxContainer();
        $c = new Cargo\Container\SingletonContainer($c);
        $c = new Cargo\Container\AutoWireContainer($c);
        $c = new Cargo\Container\AliasContainer($c);
        $this->c = $c;
    });
    it('creates auto-wired entries', function() {
        $this->c[ServiceA::class] = null;
        $res = $this->c[ServiceA::class];
        assert($res instanceof ServiceA && $res->c === $this->c && $res === $this->c[ServiceA::class]);
    });
    it('tries to resolve missing entries', function() {
        $res = $this->c[ServiceA::class];
        assert($res instanceof ServiceA && $res === $this->c[ServiceA::class]);
    });
    it('allows factory auto wired entries', function() {
        $this->c->factory(ServiceA::class);
        $res = $this->c[ServiceA::class];
        assert($res instanceof ServiceA && $res !== $this->c[ServiceA::class]);
    });
    it('allows aliased auto wired entries', function() {
        $this->c->singleton(ServiceA::class);
        $this->c->alias(ServiceA::class, 'a');
        $res = $this->c['a'];
        assert($res instanceof ServiceA && $res === $this->c[ServiceA::class]);
    });
    it('binds classes to keys', function() {
        $this->c->singleton('a', ServiceA::class);
        assert($this->c['a'] instanceof ServiceA && $this->c['a'] === $this->c['a']);
    });
});
describe('FreezingContainer', function() {
    beforeEach(function() {
        $c = new Cargo\Container\BoxContainer();
        $c = new Cargo\Container\SingletonContainer($c);
        $c = new Cargo\Container\FreezingContainer($c);
        $c = new Cargo\Container\BoxFactoryContainer($c);
        $c = new Cargo\Container\AliasContainer($c);
        $this->c = $c;
    });
    it('freezes entries from being re-defined after they called', function() {
        $this->c['a'] = function() {
            return 1;
        };
        $this->c['a'];
        try {
            $this->c['a'] = function() {};
            assert(false);
        } catch (Cargo\Exception\ContainerException $e) {
            assert(true);
        }
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
describe('protect', function() {
    it('creates value boxes always', function() {
        $c = Cargo\container();
        $c->protect('a', function() {});
        assert($c->box('a')->getType() == Cargo\Box::TYPE_VALUE);
    });
});
describe('fill', function() {
    it('assigns many values at once', function() {
        $c = Cargo\container();
        $c->fill([
            'a' => 1,
            'b' => 2,
        ]);
        assert($c->keys() == ['a', 'b']);
    });
});
describe('toPimple', function() {
    it('wraps the container in a pimple instance', function() {
        $c = Cargo\container();
        $c['a'] = 1;
        $p = $c->toPimple();
        $p['b'] = function() { return 1; };
        assert($p instanceof Pimple\Container && isset($p['a']) && $c->has('b'));
    });
});
describe('toInterop', function() {
    it('wraps the container in a Psr\Container\ContainerInterface instance', function() {
        $c = Cargo\container();
        $c['a'] = 1;
        $p = $c->toInterop();
        assert($p instanceof Psr\Container\ContainerInterface && $p->has('a') && $p->get('a') == 1);
    });
});
