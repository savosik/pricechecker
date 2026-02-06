<?php
$dir = __DIR__ . '/vendor/moonshine';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        if (strpos($content, 'trait ImportExportConcern') !== false) {
            echo "Found in: " . $file->getPathname() . "\n";
            preg_match('/namespace\s+(.+);/', $content, $matches);
            if (isset($matches[1])) {
                echo "Namespace: " . $matches[1] . "\n";
            }
        }
    }
}
