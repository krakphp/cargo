<?php

namespace Krak\Cargo\Container\LazyRegister;

use Krak\Cargo;
use Krak\Cargo\Exception\LazyRegisterException;
use ReflectionClass;
use SplStack;

class BuildLazyRegisterContainer extends Cargo\Container\ContainerDecorator
{
    private $lazy_config;
    private $provider_stack;

    public function __construct(Cargo\Container $container) {
        parent::__construct($container);

        $this->lazy_config = [
            'providers' => [],
            'services' => [],
        ];
        $this->provider_stack = new SplStack();
    }

    public function remove($id) {
        unset($this->lazy_config['services'][$id]);
        return $this->container->remove($id);
    }

    public function add($id, $box, array $opts = []) {
        $current_provider_class = $this->provider_stack->top();
        if (!$current_provider_class) {
            throw new LazyRegisterException("Cannot service $id outside of a Service Provider");
        }

        if (array_key_exists($id, $this->lazy_config['services'])) {
            // if a service already has a provider defined, then we need to add this provider as a dep to the current provider
            $service_provider = $this->lazy_config['services'][$id];
            if ($service_provider != $current_provider_class) {
                $this->lazy_config['providers'][$current_provider_class][] = $service_provider;
                $this->assertNoCircularDependency(
                    $id,
                    $this->lazy_config['providers'][$current_provider_class],
                    [$current_provider_class]
                );
            }
        } else {
            $this->lazy_config['services'][$id] = $current_provider_class;
        }

        $this->container->add($id, $box, $opts);
    }

    public function register(Cargo\ServiceProvider $provider, Cargo\Container $c = null) {
        $this->assertInstantiable($provider);

        $class = get_class($provider);
        $this->lazy_config['providers'][$class] = [];

        $this->provider_stack->push($class);
        $this->container->register($provider, $c ?: $this);
        $this->provider_stack->pop();
    }

    public function exportLazyConfig() {
        return $this->lazy_config;
    }

    private function assertInstantiable($provider) {
        $rc = new ReflectionClass($provider);
        if ($rc->getConstructor()) {
            throw new LazyRegisterException("Service Provider {$rc->getName()} must not have any constructor.");
        }
    }

    private function assertNoCircularDependency($id, $dependencies, array $cycle_stack) {
        foreach ($dependencies as $dep) {
            if (in_array($dep, $cycle_stack)) {
                $cycle_stack[] = $dep;
                throw new LazyRegisterException("Service $id introduced a circular dependency: " . implode(' -> ', $cycle_stack));
            } else {
                $this->assertNoCircularDependency($id, $this->lazy_config['providers'][$dep], array_merge($cycle_stack, [$dep]));
            }
        }
    }
}
