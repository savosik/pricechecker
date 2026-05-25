<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\RrpViolationMail;
use App\Models\ProductLink;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendRrpViolationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public ProductLink $productLink,
        public float $currentPrice,
    ) {}

    public function handle(): void
    {
        try {
            $seller = $this->productLink->seller;
            $product = $this->productLink->product;
            $marketplace = $this->productLink->marketplace;

            if (! $seller || ! $seller->email) {
                Log::warning("SendRrpViolationJob: seller has no email for ProductLink {$this->productLink->id}");
                return;
            }

            Mail::to($seller->email)->send(new RrpViolationMail(
                $product,
                $marketplace,
                $seller,
                $this->currentPrice,
                $product->recommended_price,
                $this->productLink->url,
            ));

            $this->productLink->update(['rrp_notified_at' => now()]);

            Log::info("SendRrpViolationJob: RRP violation email sent to {$seller->email} for product {$product->id}");
        } catch (\Throwable $e) {
            Log::error("SendRrpViolationJob: Error for ProductLink {$this->productLink->id}: " . $e->getMessage());
        }
    }
}
