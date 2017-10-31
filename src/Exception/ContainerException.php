<?php

namespace Krak\Cargo\Exception;

use Psr\Container;
use RuntimeException;

class ContainerException extends RuntimeException implements Container\ContainerExceptionInterface
{

}
