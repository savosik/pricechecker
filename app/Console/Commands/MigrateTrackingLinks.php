<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\ProductLink;
use App\Models\PriceHistory;
use App\Models\Marketplace;

class MigrateTrackingLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-tracking-links';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate tracking URLs from JSON to ProductLink entity';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $products = Product::whereNotNull('tracking_urls')->get();

        $this->info("Found {$products->count()} products with tracking URLs.");

        foreach ($products as $product) {
            $trackingUrls = $product->tracking_urls;

            if (!is_array($trackingUrls)) {
                continue;
            }

            foreach ($trackingUrls as $linkData) {
                if (empty($linkData['url']) || empty($linkData['marketplace_id'])) {
                    continue;
                }

                $url = $linkData['url'];
                $marketplaceId = $linkData['marketplace_id'];

                $productLink = ProductLink::firstOrCreate([
                    'product_id' => $product->id,
                    'marketplace_id' => $marketplaceId,
                    'url' => $url,
                ], [
                    'settings' => [],
                ]);

                // Update PriceHistory
                $updated = PriceHistory::where('product_id', $product->id)
                    ->where('url', $url)
                    ->update(['product_link_id' => $productLink->id]);
                
                $this->info("Migrated link for product {$product->id} - {$url}. Updated {$updated} history records.");
            }
        }

        $this->info('Migration completed.');
    }
}
