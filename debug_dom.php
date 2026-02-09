<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$task = DB::table('dom_tasks')->orderBy('id', 'desc')->first();
$dom = $task->dom_content;

// Get webPrice block
if (preg_match('/data-widget="webPrice"[^>]*>(.{0,3000})/s', $dom, $widgetMatch)) {
    $priceBlock = $widgetMatch[1];
    
    echo "=== webPrice block text ===\n";
    $text = strip_tags($priceBlock);
    $text = preg_replace('/\s+/', ' ', trim($text));
    echo $text . "\n\n";
    
    // Current regex
    echo "=== Current regex matches ([\d\s]+[,.]?\d*)\s*₽ ===\n";
    if (preg_match_all('/([\d\s]+[,.]?\d*)\s*₽/u', $priceBlock, $m)) {
        foreach ($m[1] as $i => $raw) {
            $hex = bin2hex($raw);
            echo "  Match $i: '$raw' (hex: $hex) => parsePrice: " . parsePrice($raw) . "\n";
        }
    }
    
    // Better regex that requires at least 2 digits or digit+space+digit
    echo "\n=== Improved regex ([\d][\d\s\x{00A0}]*[,.]?\d*)\s*₽ ===\n";
    if (preg_match_all('/([\d][\d\s\x{00A0}]*[,.]?\d*)\s*₽/u', $priceBlock, $m)) {
        foreach ($m[1] as $i => $raw) {
            echo "  Match $i: '$raw' => parsePrice: " . parsePrice($raw) . "\n";
        }
    }
    
    // Even better - require digits to look like a price (at least 2 digits total)
    echo "\n=== Strict regex (\d[\d\s\x{00A0}.,]*\d)\s*₽ (min 2 digits) ===\n";
    if (preg_match_all('/(\d[\d\s\x{00A0}.,]*\d)\s*₽/u', $priceBlock, $m)) {
        foreach ($m[1] as $i => $raw) {
            echo "  Match $i: '$raw' => parsePrice: " . parsePrice($raw) . "\n";
        }
    }
    
    // Show exact hex of first 200 chars of price block for character analysis  
    echo "\n=== First span with price (hex analysis) ===\n";
    if (preg_match('/tsHeadline\d+\w*">([^<]+)</u', $priceBlock, $spanMatch)) {
        $content = $spanMatch[1];
        echo "Content: '$content'\n";
        echo "Hex: ";
        for ($i = 0; $i < strlen($content); $i++) {
            echo sprintf('%02x ', ord($content[$i]));
        }
        echo "\n";
    }
}

function parsePrice(string $priceStr): float
{
    $cleaned = preg_replace('/[\s\x{00A0}]+/u', '', $priceStr);
    $cleaned = str_replace(',', '.', $cleaned);
    if (substr_count($cleaned, '.') > 1) {
        $lastDot = strrpos($cleaned, '.');
        $cleaned = str_replace('.', '', substr($cleaned, 0, $lastDot)) . substr($cleaned, $lastDot);
    }
    return (float) $cleaned;
}
