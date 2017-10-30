<?php

namespace Krak\CargoV2\Exception;

use Psr\Container;

class NotFoundException extends ContainerException implements Container\NotFoundExceptionInterface
{

}
