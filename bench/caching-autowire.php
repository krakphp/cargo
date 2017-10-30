<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/services.php';

use Krak\Cargo;

$bm = new Lavoiesl\PhpBenchmark\Benchmark();
$bm->add('auto-wire', function() {
    $unbox = new Cargo\Unbox\ServiceUnbox();
    $unbox = new Cargo\Unbox\AutoWireUnbox($unbox);
    $c = new Cargo\Container\BoxContainer($unbox);
    $c = new Cargo\Container\AutoWireContainer($c);
    $c->get('ServiceJ');
});
$bm->add('cached', function() {
    $c = new Cargo\Container\BoxContainer();
    $c[ServiceA::class] = function() { return new ServiceA(); };
    $c[ServiceB::class] = function($c) { return new ServiceB($c->get('ServiceA')); };
    $c[ServiceC::class] = function($c) { return new ServiceC($c->get('ServiceB')); };
    $c[ServiceD::class] = function($c) { return new ServiceD($c->get('ServiceC')); };
    $c[ServiceE::class] = function($c) { return new ServiceE($c->get('ServiceD')); };
    $c[ServiceF::class] = function($c) { return new ServiceF($c->get('ServiceE')); };
    $c[ServiceG::class] = function($c) { return new ServiceG($c->get('ServiceF')); };
    $c[ServiceH::class] = function($c) { return new ServiceH($c->get('ServiceG')); };
    $c[ServiceI::class] = function($c) { return new ServiceI($c->get('ServiceH')); };
    $c[ServiceJ::class] = function($c) { return new ServiceJ($c->get('ServiceI')); };
    $c->get('ServiceJ');
});

$bm->run();
