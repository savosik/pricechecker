<?php
require __DIR__ . '/vendor/autoload.php';

$classes = [
    'MoonShine\Handlers\ExportHandler',
    'MoonShine\Laravel\Handlers\ExportHandler',
    'MoonShine\ImportExport\ExportHandler',
    'MoonShine\Export\ExportHandler',
];

foreach ($classes as $class) {
    if (class_exists($class)) {
        echo "Found: $class\n";
    } else {
        echo "Not found: $class\n";
    }
}
