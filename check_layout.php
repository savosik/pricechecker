<?php
require __DIR__ . '/vendor/autoload.php';

$classes = [
    'MoonShine\Laravel\Layouts\CompactLayout',
    'MoonShine\Layouts\CompactLayout',
];

foreach ($classes as $class) {
    if (class_exists($class)) {
        echo "Class exists: $class\n";
    } else {
        echo "Class does NOT exist: $class\n";
    }
}
