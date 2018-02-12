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

function serviceB($val = 1) {
    return function() use ($val) {
        return $val;
    };
}

Krak\Cargo\bootstrapContainerMethods();

describe('Auto Wire', function() {
    beforeEach(function() {
        $this->c = Cargo\containerFactory()->autoWire()->alias()->create();
    });
    it('creates auto-wired entries', function() {
        $this->c->singleton(ServiceA::class);
        $res = $this->c[ServiceA::class];
        assert($res instanceof ServiceA && $res->c === $this->c && $res === $this->c[ServiceA::class]);
    });
    it('allows functional services', function() {
        $this->c->singleton('serviceB');
        $res = $this->c->get('serviceB');
        assert($res() == 1);
    });
    it('allows parameters to be passed to service creation', function() {
        $this->c->singleton('serviceB');
        $res = $this->c->make('serviceB', ['val' => 2]);
        assert($res() == 2);
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
