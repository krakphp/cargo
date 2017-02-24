<?php

namespace Krak\Cargo\Exception;

use Psr\Container;

class NotFoundException extends ContainerException implements Container\NotFoundExceptionInterface
{

}
