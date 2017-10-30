<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/services.php';

use Krak\Cargo;
use Krak\CargoV2;

function getFillValues() {
    return [
        'a' => 1,
        'b' => 2,
        'c' => 3,
        'd' => 4,
        'e' => 5,
        'f' => 6,
        'h' => 7,
        'i' => 8,
        'j' => 9,
        'k' => 10,
    ];
}

function registerServices($c) {
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
}

$bm = new Lavoiesl\PhpBenchmark\Benchmark();
$bm->add('v1', function() {
    $c = Cargo\container(getFillValues());
    registerServices($c);
    $c->get('a');
    $c->get('ServiceJ');
});
$bm->add('v2', function() {
    $c = CargoV2\container(getFillValues());
    registerServices($c);
    $c->get('a');
    $c->get('ServiceJ');
});

$bm->run();
