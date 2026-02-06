<?php
require __DIR__ . '/vendor/autoload.php';

$classes = [
    'MoonShine\QueryTags\QueryTag',
    'MoonShine\Laravel\QueryTags\QueryTag',
    'MoonShine\UI\Components\QueryTag',
];

foreach ($classes as $class) {
    if (class_exists($class)) {
        echo "Class exists: $class\n";
    } else {
        echo "Class does NOT exist: $class\n";
    }
}
