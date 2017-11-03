<?php

use Krak\Cargo\{
    Container\BoxContainer,
    Container\AutoWireCompile\CompileAutoWireServices,
    Container\AutoWireCompile\GenerateServiceProvider,
    Exception\CompileAutoWireException
};

class AutoWireServiceA {

}

class AutoWireServiceAA {

}

class AutoWireServiceAB {
    public function __construct(AutoWireServiceBA $a) {}
}

class AutoWireServiceBA {
    public function __construct($a) {}
}

function autoWireServiceB() {
    return function() {};
}

function autoWireServiceNested(AutoWireServiceA $service, AutoWireServiceAA $service_aa) {
    return function() {};
}

function autoWireServiceC($a, $b = 1) {
    return function() use ($a, $b) {
        return $a + $b;
    };
}

describe('Compile Auto Wire Services', function() {
    it('returns a compiled structure for auto wire services', function() {
        $c = new BoxContainer();
        $c->add('a', 1);
        $c->singleton('b', 'AutoWireServiceA');
        $c->factory('c', 'autoWireServiceB');

        $compile = new CompileAutoWireServices();
        $compiled = $compile->compile($c);
        expect($compiled)->to->equal([
            'b' => [
                'type' => 'class',
                'args' => [],
                'name' => 'AutoWireServiceA',
                'opts' => ['factory' => false, 'service' => true]
            ],
            'c' => [
                'type' => 'func',
                'args' => [],
                'name' => 'autoWireServiceB',
                'opts' => ['factory' => true, 'service' => true]
            ]
        ]);
    });
    it('recursively adds services to the compiled list while excluding already added services.', function() {
        $c = new BoxContainer();
        $c->singleton('a', 'autoWireServiceNested');
        $c->singleton('AutoWireServiceAA', function() {});

        $compile = new CompileAutoWireServices();
        $compiled = $compile->compile($c);
        expect($compiled)->to->equal([
            'a' => [
                'type' => 'func',
                'args' => [
                    'service' => [
                        'type' => 'service',
                        'value' => 'AutoWireServiceA',
                        'has_value' => true
                    ],
                    'service_aa' => [
                        'type' => 'service',
                        'value' => 'AutoWireServiceAA',
                        'has_value' => true
                    ]
                ],
                'name' => 'autoWireServiceNested',
                'opts' => ['factory' => false, 'service' => true]
            ],
            'AutoWireServiceA' => [
                'type' => 'class',
                'args' => [],
                'name' => 'AutoWireServiceA',
                'opts' => []
            ]
        ]);
    });
    it('allows arguments with default values', function() {
        $c = new BoxContainer();
        $c->singleton('a', 'autoWireServiceC');

        $compile = new CompileAutoWireServices();
        $compiled = $compile->compile($c);
        expect($compiled)->to->equal([
            'a' => [
                'type' => 'func',
                'args' => [
                    'a' => [
                        'type' => 'value',
                        'value' => null,
                        'has_value' => false
                    ],
                    'b' => [
                        'type' => 'value',
                        'value' => 1,
                        'has_value' => true
                    ]
                ],
                'name' => 'autoWireServiceC',
                'opts' => ['factory' => false, 'service' => true]
            ]
        ]);
    });
    it('can compile wrapped services', function() {
        $c = new BoxContainer();
        $c->singleton('a', 'autoWireServiceC');
        $c->wrap('a', function($service) {
            return $service;
        });

        $compile = new CompileAutoWireServices();
        $compiled = $compile->compile($c);
        expect($compiled)->to->equal([
            'a' => [
                'type' => 'func',
                'args' => [
                    'a' => [
                        'type' => 'value',
                        'value' => null,
                        'has_value' => false
                    ],
                    'b' => [
                        'type' => 'value',
                        'value' => 1,
                        'has_value' => true
                    ]
                ],
                'name' => 'autoWireServiceC',
                'opts' => ['factory' => false, 'service' => true]
            ]
        ]);
    });
    it('throws an exception if a nested service is not instantiable', function() {
        expect(function() {
            $c = new BoxContainer();
            $c->singleton('a', 'AutoWireServiceAB');
            $compile = new CompileAutoWireServices();
            $compile->compile($c);
        })->to->throw(CompileAutoWireException::class, 'Service AutoWireServiceBA is not instantiable because argument a has no default value and cannot be loaded from the service container. The dependency tree is: AutoWireServiceAB -> AutoWireServiceBA');
    });
    it('throws an exception if auto wire type is not a class or func', function() {
        expect(function() {
            $c = new BoxContainer();
            $c->singleton('a', 'foo');
            $compile = new CompileAutoWireServices();
            $compile->compile($c);
        })->to->throw(CompileAutoWireException::class, 'Auto Wire service value foo is not a class or function name.');
    });
});
describe('Generate Compiled Service Provider', function() {
    it('can generate a service provider from the cached definition', function() {
        $c = new BoxContainer();
        $c->singleton('a', 'autoWireServiceC');
        $c->wrap('a', function($service) {
            return function() use ($service) {
                return $service() * 2;
            };
        });

        $compile = new CompileAutoWireServices();
        $compiled = $compile->compile($c);
        $generate = new GenerateServiceProvider();
        list($class, $contents) = $generate->generateServiceProvider($compiled);
        echo($contents);

        eval(substr($contents, 7));
        $c->register(new $class());
        $service = $c->make('a', ['a' => 1, 'b' => 5]);
        expect($service())->to->equal(12);
    });
});
