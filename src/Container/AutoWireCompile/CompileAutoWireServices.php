<?php

namespace Krak\Cargo\Container\AutoWireCompile;

use Closure;
use Krak\Cargo;
use Krak\Cargo\Exception\CompileAutoWireException;
use Psr\Log;
use ReflectionClass;
use ReflectionFunction;
use SplStack;

class CompileAutoWireServices
{
    private $dep_stack;

    public function __construct() {
        $this->dep_stack = new SplStack();
    }

    /** @throws CompileAutoWireException */
    public function compile(Cargo\Container $container, Log\LoggerInterface $logger = null) {
        $logger = $logger ?: new Log\NullLogger();

        $compiled = [];

        foreach ($this->autoWiredServices($container) as $tup) {
            list($key, $box) = $tup;
            $compiled = $this->defineAutoWireService($container, $compiled, $key, $box, $logger);
        }

        return $compiled;
    }

    private function defineAutoWireService($container, $compiled, $key, $box, $logger) {
        $logger->info("Compiling service $key");

        $this->dep_stack->push($box[0]);

        $type = $this->getBoxType($box[0]);
        $def = [
            'type' => $type,
            'args' => $this->buildServiceArgs($type, $box),
            'name' => $box[0],
            'opts' => $box[1]
        ];

        $compiled[$key] = $def;

        $is_child = count($this->dep_stack) > 1;

        foreach ($def['args'] as $arg_name => $arg) {
            // check if non-instantiable
            if ($is_child && !$arg['has_value']) {
                $dep_tree = array_reverse(iterator_to_array($this->dep_stack));
                throw new CompileAutoWireException("Service $key is not instantiable because argument {$arg_name} has no default value and cannot be loaded from the service container. The dependency tree is: " . implode(' -> ', $dep_tree));
            }

            if ($arg['type'] == 'service' && !$container->has($arg['value'])) {
                $compiled = $this->defineAutoWireService($container, $compiled, $arg['value'], [
                    $arg['value'],
                    []
                ], $logger);
            }
        }

        $this->dep_stack->pop();

        return $compiled;
    }

    private function buildServiceArgs($type, $box) {
        if ($type == 'class') {
            $rc = new ReflectionClass($box[0]);
            $rf = $rc->getConstructor(); // reflection function abstract
            if (!$rf) {
                return [];
            }
        } else {
            $rf = new ReflectionFunction($box[0]);
        }

        $params = [];
        foreach ($rf->getParameters() as $arg_meta) {
            $params[$arg_meta->getName()] = $this->buildArg($arg_meta);
        }

        return $params;
    }

    private function buildArg($arg_meta) {
        $type = $arg_meta->getClass() ? 'service' : 'value';
        $has_value = $type == 'service' ? true : ($arg_meta->isOptional() && $arg_meta->isDefaultValueAvailable());
        return [
            'type' => $type,
            'value' => $type == 'service' ? $arg_meta->getClass()->getName() : ($has_value ? $arg_meta->getDefaultValue() : null),
            'has_value' => $has_value
        ];
    }

    /** yields all of the auto wired services that need to be built */
    private function autoWiredServices($container) {
        foreach ($container->keys() as $key) {
            $box = $container->box($key);
            $box = Cargo\Container\unwrapBox($box);
            if (is_string($box[0]) && Cargo\Container\optsService($box[1])) {
                yield [$key, $box];
            }
        }
    }

    /** @throws CompileAutoWireException */
    private function getBoxType($value) {
        if (class_exists($value)) {
            return 'class';
        }
        if (function_exists($value)) {
            return 'func';
        }

        throw new CompileAutoWireException("Auto Wire service value $value is not a class or function name.");
    }
}
