<?php

namespace Krak\Cargo\Container\AutoWireCompile;

class GenerateServiceProvider
{
    public function generateServiceProvider(array $compiled) {
        list($class_name, $class_def_tpl) = $this->createClassDefinition($compiled);

        $service_provider_content = '';
        foreach ($compiled as $key => $def) {
            $service_provider_content .= $this->createServiceDefinition($key, $def) . "\n";
        }

        return [$class_name, sprintf($class_def_tpl, $service_provider_content)];
    }

    private function createClassDefinition($compiled) {
        $tpl = <<<TPL
<?php

namespace Krak\Cargo;

class %s implements \Krak\Cargo\ServiceProvider
{
    public function register(\Krak\Cargo\Container \$container) {
%%s
    }
}
TPL;
        $hash = md5(json_encode($compiled));
        $class_name = sprintf('__Cached%sServiceProvider', $hash);
        return ["Krak\\Cargo\\$class_name", sprintf($tpl, $class_name)];
    }

    private function createServiceDefinition($key, $def) {
        $tpl = <<<TPL
        define(\$container, {key}, function(\$container, \$params) {
            \$param_defs = {param_defs};
            return {create_service}(...array_values(buildCachedParams(\$container, {key}, \$param_defs, \$params)));
        }, {opts});
TPL;
        return strtr($tpl, [
            '{key}' => '"' . $key . '"',
            '{param_defs}' => var_export($def['args'], true),
            '{create_service}' => $def['type'] == 'class'
                ? "new \\{$def['name']}"
                : "\\{$def['name']}",
            '{opts}' => var_export($def['opts'], true)
        ]);
    }
}
