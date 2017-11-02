<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Krak\Cargo\{Container, ServiceProvider};
use function Krak\Cargo\container;

function registerServices(Container $c, $num_providers, $num_services) {
    foreach (range(0, $num_providers - 1) as $i) {
        $c->register(new class($i, $num_services) implements ServiceProvider {
            public function __construct($i, $num_services) {
                $this->i = $i;
                $this->num_services = $num_services;
            }

            public function register(Container $c) {
                foreach (range(0, $this->num_services - 1) as $j) {
                    $c->singleton(sprintf("service_%d_%d", $this->i, $j), function() {

                    });
                }
            }
        });
    }
}

function registerEmptyServices(Container $c, $num_providers) {
    foreach (range(0, $num_providers - 1) as $i) {
        $c->register(new class implements ServiceProvider {
            public function register(Container $c) {

            }
        });
    }
}

function dumpLazyFile(Container $c) {
    $keys = $c->keys();
    $lazy_file = array_reduce($keys, function($acc, $key) {
        $acc[$key] = ['ServiceProvider'];
        return $acc;
    }, []);

    file_put_contents(
        __DIR__ . '/Resources/lazy.php',
        sprintf("<?php\n\nreturn %s;", var_export($lazy_file, true))
    );
}

const NUM_PROVIDERS = 5;
const NUM_SERVICES = 10;

// $c = container();
// registerServices($c, NUM_PROVIDERS, NUM_SERVICES);
// dumpLazyFile($c);

$bm = new Lavoiesl\PhpBenchmark\Benchmark();
$bm->add('normal', function() {
    $c = container();
    registerServices($c, NUM_PROVIDERS, NUM_SERVICES);
});
$bm->add('lazy', function() {
    $c = container();
    $lazy_file = include __DIR__ . '/Resources/lazy.php';
    registerEmptyServices($c, NUM_PROVIDERS);
});

$bm->run();
