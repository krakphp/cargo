<?php

namespace Krak\Cargo\Container;

use Krak\Arr,
    Krak\Cargo,
    SplObjectStorage;

class SingletonContainer extends AbstractContainer
{
    private $container;
    private $default_singleton;
    private $cache;

    public function __construct(Cargo\Container $container, $default_singleton = true) {
        $this->container = $container;
        $this->default_singleton = $default_singleton;
        $this->cache = new SplObjectStorage();
    }

    public function get($id, Cargo\Container $container = null) {
        $box = $this->container->box($id);
        if (!$this->cache->contains($box)) {
            return $this->container->get($id, $container ?: $this);
        }

        $res = $this->cache[$box];
        if ($res !== null) {
            return $res;
        }

        $res = $this->container->get($id, $container ?: $this);
        if ($res === null) {
            throw new Cargo\Exception\ContainerException("Cannot create singleton for '$id' because it resolves to null");
        }
        $this->cache[$box] = $res;
        return $res;
    }
    public function has($id) {
        return $this->container->has($id);
    }
    public function remove($id) {
        $box = $this->box($id);
        $this->cache->detach($box);
        return $this->container->remove($id);
    }
    public function add($id, $box, array $opts = []) {
        if ($box->getType() != Cargo\Box::TYPE_SERVICE || $this->cache->contains($box)) {
            return $this->container->add($id, $box, $opts);
        }

        $singleton = Arr\get($opts, 'singleton', $this->default_singleton);
        if (Arr\get($opts, 'wrapped', false)) {
            $singleton = $this->maybeStoreWrappedBox($id, $box);
        }

        if ($singleton) {
            $this->cache->attach($box);
        }

        return $this->container->add($id, $box, $opts);
    }
    public function box($id) {
        return $this->container->box($id);
    }
    public function keys() {
        return $this->container->keys();
    }

    private function maybeStoreWrappedBox($id, $box) {
        $orig_box = $this->container->box($id);
        if (!$this->cache->contains($orig_box)) {
            return false;
        }

        $this->cache->detach($orig_box);

        return true;
    }
}
