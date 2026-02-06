<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\PriceHistory;

$history = PriceHistory::first();
if (!$history) {
    echo "No PriceHistory records found in the entire database.\n";
    exit;
}

echo "Found PriceHistory ID: " . $history->id . " for Product ID: " . $history->product_id . "\n";

$product = Product::find($history->product_id);
if (!$product) {
    echo "Product ID " . $history->product_id . " not found!\n";
    exit;
}

echo "Product Name: " . $product->name . "\n";

$historyCount = PriceHistory::where('product_id', $product->id)->count();
echo "Actual PriceHistory count in DB for this product: " . $historyCount . "\n";

$productWithCount = Product::withCount('priceHistories')->find($product->id);
echo "Loaded price_histories_count: " . ($productWithCount->price_histories_count ?? 'null') . "\n";
