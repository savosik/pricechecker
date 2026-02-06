<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\DomTask;
use App\Models\PriceHistory;
use App\Models\Seller;
use App\Services\PriceParser\DomPriceExtractor;
use App\Services\PriceParser\PriceData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessDomJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public DomTask $domTask
    ) {}

    public function handle(DomPriceExtractor $extractor): void
    {
        Log::info("ProcessDomJob: Starting for task {$this->domTask->id}");
        
        try {
            $dom = $this->domTask->dom_content;
            $marketplaceCode = $this->domTask->marketplace_code;
            
            if (empty($dom)) {
                Log::warning("ProcessDomJob: Empty DOM for task {$this->domTask->id}");
                return;
            }
            
            // Extract price based on marketplace
            $priceData = match ($marketplaceCode) {
                'ozon' => $extractor->extractOzonPrice($dom),
                'wildberries' => $extractor->extractWildberriesPrice($dom),
                default => null,
            };
            
            if ($priceData === null) {
                Log::warning("ProcessDomJob: Could not extract price from DOM", [
                    'task_id' => $this->domTask->id,
                    'marketplace' => $marketplaceCode,
                ]);
                return;
            }
            
            $productLink = $this->domTask->productLink;
            if (!$productLink) {
                Log::warning("ProcessDomJob: No product link for task {$this->domTask->id}");
                return;
            }
            
            $product = $productLink->product;
            
            // Find or create seller if present
            $sellerId = null;
            if ($priceData->sellerData !== null) {
                $seller = Seller::firstOrCreate(
                    ['external_id' => $priceData->sellerData->externalId],
                    [
                        'name' => $priceData->sellerData->name,
                        'inn' => $priceData->sellerData->inn,
                    ]
                );
                $sellerId = $seller->id;
            }
            
            // Finding duplicate price
            $lastHistory = PriceHistory::where('product_link_id', $productLink->id)
                ->latest()
                ->first();

            if ($lastHistory && 
                (float)$lastHistory->user_price === (float)$priceData->userPrice &&
                (float)$lastHistory->base_price === (float)$priceData->basePrice
            ) {
                 $lastHistory->update(['checked_at' => now()]);
                 Log::info("ProcessDomJob: Price duplicate for task {$this->domTask->id}. Updated checked_at and skipping save/notify.");
                 return;
            }

            // Check conditions before saving (similar to ParseProductPriceJob)
            if ($this->shouldSavePrice($priceData)) {
                $history = PriceHistory::create([
                    'product_id' => $product->id,
                    'marketplace_id' => $productLink->marketplace_id,
                    'product_link_id' => $productLink->id,
                    'user_price' => $priceData->userPrice,
                    'base_price' => $priceData->basePrice,
                    'seller_id' => $sellerId,
                    'url' => $this->domTask->url,
                    'checked_at' => now(),
                ]);
                
                Log::info("ProcessDomJob: Saved PriceHistory ID: {$history->id} for task {$this->domTask->id}");
                
                // Check notification conditions
                Log::info("ProcessDomJob: Checking notify conditions for product {$product->id} on marketplace {$productLink->marketplace->name}");
                if ($this->shouldNotifyAdmins($priceData)) {
                    Log::info("ProcessDomJob: Notify conditions met! Dispatching SendPriceNotificationJob");
                    SendPriceNotificationJob::dispatch(
                        $product,
                        $productLink->marketplace,
                        $priceData->userPrice,
                        $priceData->basePrice,
                        $this->domTask->url
                    );
                 } else {
                    Log::info("ProcessDomJob: Notify conditions NOT met for product {$product->id}");
                }
            }
            
        } catch (\Throwable $e) {
            Log::error("ProcessDomJob: Error for task {$this->domTask->id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
    
    private function shouldSavePrice(PriceData $priceData): bool
    {
        $productLink = $this->domTask->productLink;
        if (!$productLink) {
            return true;
        }
        
        $product = $productLink->product;
        $conditions = $product->condition;

        if (!$conditions || $conditions->isEmpty()) {
            return true;
        }

        $mpConditions = $conditions->filter(function ($condition) use ($productLink) {
            return isset($condition['marketplace_id']) && 
                   (int)$condition['marketplace_id'] === (int)$productLink->marketplace_id;
        });

        if ($mpConditions->isEmpty()) {
            return true;
        }

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
            'eq', '=' => (abs($price - $value) < 0.001),
            default => false,
        };
    }

    private function shouldNotifyAdmins(PriceData $priceData): bool
    {
        $productLink = $this->domTask->productLink;
        if (!$productLink) {
            return false;
        }
        
        $product = $productLink->product;
        $notifyConditions = $product->notify_condition;

        if (!$notifyConditions || $notifyConditions->isEmpty()) {
            return false;
        }

        $mpConditions = $notifyConditions->filter(function ($condition) use ($productLink) {
            return isset($condition['marketplace_id']) && 
                   (int)$condition['marketplace_id'] === (int)$productLink->marketplace_id;
        });

        if ($mpConditions->isEmpty()) {
            return false;
        }

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
