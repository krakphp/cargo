<?php

require_once __DIR__ . '/../vendor/autoload.php';

$bm = new Lavoiesl\PhpBenchmark\Benchmark();

function a(Service $s) {
    return $s->id;
}

class Service {
    public static $methods = [];
    public $id;

    public function __construct($id) {
        $this->id = $id;
    }

    public function a() {
        return a($this);
    }

    public function __call($method, array $args) {
        if (!isset(self::$methods[$method])) {
            throw new \BadMethodCallException($method);
        }

        return self::$methods[$method]($this, ...$args);
    }
}

Service::$methods['b'] = 'a';

$bm->add('simple-wrap', function() {
    $s = new Service(1);
    $s->a();
});
$bm->add('auto-wrap', function() {
    $s = new Service(1);
    $s->b();
});

$bm->run();
