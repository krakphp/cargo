<?php

namespace Krak\Cargo\Exception;

class BoxFrozenException extends ContainerException
{
    private $id;

    public function __construct($id) {
        $this->id = $id;
        parent::__construct("Box $id cannot be set because it has been frozen.");
    }

    public function getId() {
        return $this->id;
    }
}
