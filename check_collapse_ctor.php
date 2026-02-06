<?php
require __DIR__ . '/vendor/autoload.php';

$class = 'MoonShine\UI\Components\Collapse';

if (class_exists($class)) {
    $reflection = new ReflectionClass($class);
    $constructor = $reflection->getConstructor();
    if ($constructor) {
        echo "Constructor parameters:\n";
        foreach ($constructor->getParameters() as $param) {
            echo "  " . $param->getName() . "\n";
        }
    } else {
        echo "No constructor found.\n";
    }
}
