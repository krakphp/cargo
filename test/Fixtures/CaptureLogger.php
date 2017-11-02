<?php

namespace Krak\Cargo\Test\Fixtures;

use Psr\Log\{LoggerInterface, LoggerTrait};

class CaptureLogger implements LoggerInterface
{
    use LoggerTrait;

    public $logs = [];
    public function log($level, $msg, array $params = []) {
        $this->logs[] = func_get_args();
    }
}
