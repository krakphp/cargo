<?php

namespace Krak\Cargo\Container\LazyRegister;

use Closure;
use Krak\Cargo;

class BuildLazyRegister
{
    private $output_path;
    private $file_put_contents;

    public function __construct($output_path, $file_put_contents = 'file_put_contents') {
        $this->output_path = $output_path;
        $this->file_put_contents = $file_put_contents;
    }

    public function buildLazyRegister(Cargo\Container $container, Closure $register_services) {
        $build_lazy_container = new BuildLazyRegisterContainer($container);
        $register_services($build_lazy_container);

        $config = $build_lazy_container->exportLazyConfig();
        $file_put_contents = $this->file_put_contents;
        $file_put_contents($this->output_path, sprintf("<?php\n\nreturn %s;", var_export($config, true)));
    }
}
