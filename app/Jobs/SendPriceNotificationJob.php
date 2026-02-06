<?php

namespace App\Jobs;

use App\Mail\PriceChangedMail;
use App\Models\Marketplace;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\Notifications\MoonShineNotification;
use MoonShine\Crud\Notifications\NotificationButton;

class SendPriceNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Product $product,
        public Marketplace $marketplace,
        public float $userPrice,
        public float $basePrice,
        public string $url
    ) {}

    public function handle(): void
    {
        try {
            $admins = MoonshineUser::all();
            if ($admins->isEmpty()) {
                Log::warning("No admin users for price notification");
                return;
            }

            $message = "Цена: {$this->product->name} на {$this->marketplace->name}. Базовая: {$this->basePrice} р, Пользовательская: {$this->userPrice} р";
            $link = config("app.url") . "/admin/resource/product-resource/{$this->product->id}";

            MoonShineNotification::send(
                message: $message,
                button: new NotificationButton("Смотреть", $link),
                ids: $admins->pluck("id")->toArray()
            );
            Log::info("Sent MoonShine notification to admins");

            foreach ($admins as $admin) {
                if ($admin->email) {
                    Mail::to($admin->email)->send(new PriceChangedMail($this->product, $this->marketplace, $this->userPrice, $this->basePrice, $this->url));
                    Log::info("Sent email to: {$admin->email}");
                }
            }

            Log::info("Price notifications sent for product {$this->product->id}");
        } catch (\Throwable $e) {
            Log::error("Error sending notifications: " . $e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
}
