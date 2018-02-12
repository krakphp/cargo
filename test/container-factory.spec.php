<?php

use Krak\Cargo;

Krak\Cargo\bootstrapContainerMethods();

describe('Container Factory', function() {
    it('can build a simple container', function() {
        $c = Cargo\containerFactory()->autoWire(false)->create();
        expect($c)->to->be->an->instanceof(Cargo\Container\BoxContainer::class);
    });
    it('can build an env container', function() {
        $c = Cargo\containerFactory()->autoWire(false)->env()->create();
        putenv('A=1');
        $c->env('A');
        expect($c->get('A'))->to->equal('1');
    });
    it('can build an auto-wire container', function() {
        $c = Cargo\containerFactory()->autoWire()->create();
        expect($c->get('StdClass'))->instanceof('StdClass');
    });
    it('can create a logger container', function() {
        $c = Cargo\containerFactory()->autoWire(false)->log(new Cargo\Test\Fixtures\CaptureLogger())->create();
        expect($c)->instanceof(Cargo\Container\LoggingContainer::class);
    });
    it('can create a detect-cycles container', function() {
        $c = Cargo\containerFactory()->autoWire(false)->detectCycles()->create();
        expect($c)->instanceof(Cargo\Container\DetectCyclesContainer::class);
    });
    it('can create a lazy container', function() {
        $c = Cargo\containerFactory()->autoWire(false)->lazy(['services' => [], 'providers' => []])->create();
        expect($c)->instanceof(Cargo\Container\LazyRegisterContainer::class);
    });
});
