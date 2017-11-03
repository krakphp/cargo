<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/services.php';

use Krak\Cargo;

function generateServiceProvider() {
    $c = Cargo\container();
    $c->singleton('ServiceJ');
    $compile = new Cargo\Container\AutoWireCompile\CompileAutoWireServices();
    $compiled = $compile->compile($c);

    $generate = new Cargo\Container\AutoWireCompile\GenerateServiceProvider();
    list($class, $contents) = $generate->generateServiceProvider($compiled);

    file_put_contents(__DIR__ . '/Resources/service-provider.php', $contents);
    file_put_contents(__DIR__ . '/Resources/cached-config.php', sprintf("<?php\n\nreturn %s;", var_export([
        'class' => $class,
        'filename' => __DIR__ . '/Resources/service-provider.php'
    ], true)));
}

if (isset($argv[1])) {
    generateServiceProvider();
    exit;
}


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
    $config = include __DIR__ . '/Resources/cached-config.php';
    require_once $config['filename'];
    $c->register(new $config['class']);
    $c->get('ServiceJ');
});

$bm->run();
