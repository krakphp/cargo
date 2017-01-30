<?php

namespace Krak\Cargo\Container\Wrapper;

use Krak\Cargo,
    Pimple;

class PimpleWrapper extends Pimple\Container
{
    private $container;

    public function __construct(Cargo\Container $container) {
        $this->container = $container;
    }

    public function offsetSet($id, $value) {
        if (is_object($value) && !$value instanceof Cargo\Box && method_exists($value, '__invoke')) {
            $value = new Cargo\Box\CachedBox(new Cargo\Box\Pimple\FactoryBox($value, $this));
        }

        $this->container[$id] = $value;
    }

    public function offsetGet($id) {
        return $this->container[$id];
    }

    public function offsetExists($id) {
        return isset($this->container[$id]);
    }

    public function offsetUnset($id) {
        unset($this->container[$id]);
    }

    public function factory($callable) {
        if (!method_exists($callable, '__invoke')) {
            throw new \InvalidArgumentException('Service definition is not a Closure or invokable object.');
        }

        return new Cargo\Box\Pimple\FactoryBox($callable, $this);
    }

    public function protect($callable) {
        if (!method_exists($callable, '__invoke')) {
            throw new \InvalidArgumentException('Callable is not a Closure or invokable object.');
        }

        return new Cargo\Box\ValueBox($callable);
    }

    public function raw($id)
    {
        if (!isset($this->keys[$id])) {
            throw new \InvalidArgumentException(sprintf('Identifier "%s" is not defined.', $id));
        }

        return $this->container->box($id);
    }

    public function extend($id, $callable)
    {
        if (!$this->container->has($id)) {
            throw new \InvalidArgumentException(sprintf('Identifier "%s" is not defined.', $id));
        }

        if (!is_object($callable) || !method_exists($callable, '__invoke')) {
            throw new \InvalidArgumentException('Extension service definition is not a Closure or invokable object.');
        }

        return $this->container->add($id, new Cargo\Box\Pimple\ExtendBox(
            $this->container->box($id),
            $callable,
            $this
        ));
    }

    /**
     * Returns all defined value names.
     *
     * @return array An array of value names
     */
    public function keys() {
        return $this->container->keys();
    }
}
