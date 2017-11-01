<?php

namespace Krak\Cargo\Container;

use Krak\Cargo;

/** Implements Lazy Registration via the lazy_config The lazy config schema looks like: */
/* [
    'services' => [
        'a' => 'Provider1',
        'b' => 'Provider2'
    ],
    'providers' => [
        'Provider1' => [],
        'Provider2' => []
    ]
] */
class LazyRegisterContainer extends ContainerDecorator
{
    private $lazy_config;
    private $registered;

    public function __construct(Cargo\Container $container, array $lazy_config) {
        parent::__construct($container);
        $this->lazy_config = $lazy_config;
        $this->registered = [];
    }

    public static function createFromCacheFile(Cargo\Container $container, $file_path) {
        return new self($container, include $file_path);
    }

    public function make($id, array $params = [], Cargo\Container $c = null) {
        $this->lazyLoad($id);
        return $this->container->make($id, $params, $c ?: $this);
    }

    public function remove($id) {
        $this->lazyLoad($id);
        return $this->container->remove($id);
    }

    public function box($id) {
        $this->lazyLoad($id);
        return $this->container->box($id);
    }

    public function has($id) {
        return $this->container->has($id) || array_key_exists($id, $this->lazy_config['services']);
    }

    public function keys() {
        return array_unique(array_merge(array_keys($this->lazy_config['services']), $this->container->keys()));
    }

    public function register(Cargo\ServiceProvider $provider, Cargo\Container $c = null) {
        if (array_key_exists(get_class($provider), $this->lazy_config['providers'])) {
            return;
        }

        return $this->container->register($provider, $c ?: $this);
    }

    private function lazyLoad($id) {
        if ($this->container->has($id)) {
            return;
        }

        // we need to load the service provider now
        if (!array_key_exists($id, $this->lazy_config['services'])) {
            return;
        }

        $provider = $this->lazy_config['services'][$id];
        $this->loadServiceProvider($provider);
    }

    private function loadServiceProvider($provider_class) {
        if (array_key_exists($provider_class, $this->registered)) {
            return;
        }

        $deps = $this->lazy_config['providers'][$provider_class];
        foreach ($deps as $dep) {
            $this->loadServiceProvider($dep);
        }

        $provider = new $provider_class();
        $this->container->register($provider);
        $this->registered[$provider_class] = null;
    }
}
