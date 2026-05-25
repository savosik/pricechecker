<?php

namespace App\Mail;

use App\Models\Marketplace;
use App\Models\Product;
use App\Models\Seller;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RrpViolationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Product $product,
        public Marketplace $marketplace,
        public Seller $seller,
        public float $currentPrice,
        public float $recommendedPrice,
        public string $url,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Нарушение РРЦ: {$this->product->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.rrp-violation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
