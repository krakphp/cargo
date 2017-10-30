<?php

namespace Krak\CargoV2\Exception;

use Psr\Container;
use RuntimeException;

class ContainerException extends RuntimeException implements Container\ContainerExceptionInterface
{

}
