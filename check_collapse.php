<?php
require __DIR__ . '/vendor/autoload.php';

$classes = [
    'MoonShine\UI\Components\Layout\Box',
    'MoonShine\UI\Components\Collapse',
];

foreach ($classes as $class) {
    if (class_exists($class)) {
        echo "Class exists: $class\n";
        $reflection = new ReflectionClass($class);
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if (str_contains($method->name, 'collaps')) {
                echo "  Method: " . $method->name . "\n";
            }
        }
    } else {
        echo "Class does NOT exist: $class\n";
    }
}
