<?php

declare(strict_types=1);

namespace App\Jobs;


use App\Models\Marketplace;
use App\Models\PriceHistory;
use App\Models\ProductLink;
use App\Models\Seller;
use App\Services\PriceParser\PriceParserFactory;
use Illuminate\Bus\Queueable;
use App\Jobs\SendRrpViolationJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ParseProductPriceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public ProductLink $productLink
    ) {}

    public function handle(): void
    {
        Log::info("Job started for Link ID: {$this->productLink->id}");
        try {
            $marketplaceId = $this->productLink->marketplace_id;
            $url = $this->productLink->url;
            $product = $this->productLink->product;

            $marketplace = Marketplace::find($marketplaceId);
            if (! $marketplace) {
                Log::error("Marketplace not found: {$marketplaceId}");
                return;
            }

            $marketplaceCode = strtolower($marketplace->code ?? $marketplace->name);

            // For Ozon, use browser extension DOM parsing (async)
            if (str_contains($marketplaceCode, 'ozon')) {
                $this->createDomTask($url, 'ozon');
                Log::info("Created DomTask for Ozon Link ID: {$this->productLink->id}");
                $this->productLink->update(['last_parsed_at' => now(), 'last_parse_error' => null]);
                return;
            }

            // For other marketplaces, use direct parsing
            $parser = PriceParserFactory::make($marketplace);
            $priceData = $parser->getPrice($url);

            if ($priceData === null) {
                $error = "Не удалось получить цену с {$marketplace->name}";
                Log::warning("Failed to parse price for product {$product->id} on marketplace {$marketplace->name}");
                $this->productLink->update(['last_parsed_at' => now(), 'last_parse_error' => $error]);
                return;
            }

            // Priority: seller from ProductLink -> seller from DOM -> null
            $sellerId = $this->productLink->seller_id;

            if ($priceData->sellerData !== null) {
                $seller = Seller::firstOrCreate(
                    ['external_id' => $priceData->sellerData->externalId],
                    [
                        'name' => $priceData->sellerData->name,
                        'inn' => $priceData->sellerData->inn,
                    ]
                );
                Log::info("Found/Created Seller ID: {$seller->id} ({$seller->name})");
                
                // Use link seller if set, otherwise use DOM seller and assign to link
                if ($sellerId === null) {
                    $sellerId = $seller->id;
                    $this->productLink->update(['seller_id' => $sellerId]);
                    Log::info("Assigned seller '{$seller->name}' to ProductLink {$this->productLink->id}");
                }
            }

            // Check RRP violation and notify seller
            if ($this->shouldSendRrpAlert($priceData->userPrice)) {
                Log::info("ParseProductPriceJob: RRP violation for Link ID: {$this->productLink->id}. Dispatching SendRrpViolationJob.");
                SendRrpViolationJob::dispatch($this->productLink, $priceData->userPrice);
            }

            // Check for duplicate price
            $lastHistory = PriceHistory::where('product_link_id', $this->productLink->id)
                ->latest()
                ->first();

            if ($lastHistory && 
                (float)$lastHistory->user_price === (float)$priceData->userPrice &&
                (float)$lastHistory->base_price === (float)$priceData->basePrice
            ) {
                 $lastHistory->update(['checked_at' => now()]);
                 $this->productLink->update(['last_parsed_at' => now(), 'last_parse_error' => null]);
                 Log::info("ParseProductPriceJob: Price duplicate for Link ID: {$this->productLink->id}. Updated checked_at and skipping save/notify.");
                 return;
            }

            if ($this->shouldSavePrice($priceData)) {
                $history = PriceHistory::create([
                    'product_id' => $product->id,
                    'marketplace_id' => $marketplaceId,
                    'product_link_id' => $this->productLink->id,
                    'user_price' => $priceData->userPrice,
                    'base_price' => $priceData->basePrice,
                    'seller_id' => $sellerId,
                    'url' => $url,
                    'checked_at' => now(),
                ]);
                Log::info("Saved PriceHistory ID: {$history->id} for Link ID: {$this->productLink->id}");
            } else {
                Log::info("Skipping save for Link ID: {$this->productLink->id} due to conditions");
            }

            $this->productLink->update(['last_parsed_at' => now(), 'last_parse_error' => null]);

            // Check if we should notify admins
            Log::info("Checking notify conditions for product {$product->id} on marketplace {$marketplace->name}");
            if ($this->shouldNotifyAdmins($priceData)) {
                Log::info("Notify conditions met! Dispatching SendPriceNotificationJob");
                \App\Jobs\SendPriceNotificationJob::dispatch(
                    $product,
                    $marketplace,
                    $priceData->userPrice,
                    $priceData->basePrice,
                    $url
                );
            } else {
                Log::info("Notify conditions NOT met for product {$product->id}");
            }

        } catch (\Throwable $e) {
            Log::error("Error in ParseProductPriceJob for Link ID {$this->productLink->id}: " . $e->getMessage());
            Log::error($e->getTraceAsString());
            $this->productLink->update(['last_parsed_at' => now(), 'last_parse_error' => $e->getMessage()]);
        }
    }

    /**
     * Create a DOM parsing task for browser extension
     */
    private function createDomTask(string $url, string $marketplaceCode): void
    {
        // Check if pending task already exists for this link
        $exists = \App\Models\DomTask::where('product_link_id', $this->productLink->id)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            Log::info("DomTask already exists for Link ID: {$this->productLink->id}");
            return;
        }

        \App\Models\DomTask::create([
            'product_link_id' => $this->productLink->id,
            'url' => $url,
            'marketplace_code' => $marketplaceCode,
            'status' => 'pending',
        ]);
    }

    private function shouldSendRrpAlert(float $userPrice): bool
    {
        $product = $this->productLink->product;
        if (! $product->recommended_price) {
            return false;
        }
        if ($userPrice >= $product->recommended_price) {
            return false;
        }
        if (! $this->productLink->seller?->email) {
            return false;
        }
        if (! $this->productLink->seller->notify_rrp) {
            return false;
        }
        $notifiedAt = $this->productLink->rrp_notified_at;
        if ($notifiedAt && $notifiedAt->diffInDays(now()) < 7) {
            return false;
        }
        return true;
    }

    private function shouldSavePrice(\App\Services\PriceParser\PriceData $priceData): bool
    {
        $product = $this->productLink->product;
        $conditions = $product->condition;

        // If no conditions at all, save.
        if (! $conditions || $conditions->isEmpty()) {
            return true;
        }

        // Filter conditions for this marketplace
        // Ensure loose comparison for ID
        $mpConditions = $conditions->filter(function ($condition) {
            return isset($condition['marketplace_id']) && 
                   (int)$condition['marketplace_id'] === (int)$this->productLink->marketplace_id;
        });

        // If no conditions for THIS marketplace, save.
        if ($mpConditions->isEmpty()) {
            return true;
        }

        // Check if ANY condition is met (OR logic)
        foreach ($mpConditions as $condition) {
            $operator = html_entity_decode($condition['operator'] ?? '=');
            $value = (float) ($condition['value'] ?? 0);
            $priceType = $condition['price_type'] ?? 'user_price';

            $priceToCheck = $priceType === 'base_price' ? $priceData->basePrice : $priceData->userPrice;

            if ($this->checkCondition($priceToCheck, $operator, $value)) {
                return true;
            }
        }

        return false;
    }

    private function checkCondition(float $price, string $operator, float $value): bool
    {
        return match ($operator) {
            'gt', '>' => $price > $value,
            'gte', '>=' => $price >= $value,
            'lt', '<' => $price < $value,
            'lte', '<=' => $price <= $value,
            'eq', '=' => (abs($price - $value) < 0.001), // Float comparison
            default => false,
        };
    }

    private function shouldNotifyAdmins(\App\Services\PriceParser\PriceData $priceData): bool
    {
        $product = $this->productLink->product;
        $notifyConditions = $product->notify_condition;

        // If no notify conditions at all, don't notify.
        if (! $notifyConditions || $notifyConditions->isEmpty()) {
            return false;
        }

        // Filter conditions for this marketplace
        $mpConditions = $notifyConditions->filter(function ($condition) {
            return isset($condition['marketplace_id']) && 
                   (int)$condition['marketplace_id'] === (int)$this->productLink->marketplace_id;
        });

        // If no notify conditions for THIS marketplace, don't notify.
        if ($mpConditions->isEmpty()) {
            return false;
        }

        // Check if ANY notification condition is met (OR logic)
        foreach ($mpConditions as $condition) {
            $operator = html_entity_decode($condition['operator'] ?? '=');
            $value = (float) ($condition['value'] ?? 0);
            $priceType = $condition['price_type'] ?? 'user_price';

            $priceToCheck = $priceType === 'base_price' ? $priceData->basePrice : $priceData->userPrice;

            if ($this->checkCondition($priceToCheck, $operator, $value)) {
                return true;
            }
        }

        return false;
    }
}
