<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DomTask extends Model
{
    protected $fillable = [
        'product_link_id',
        'url',
        'marketplace_code',
        'status',
        'worker_id',
        'dom_content',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function productLink(): BelongsTo
    {
        return $this->belongsTo(ProductLink::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function markAsProcessing(string $workerId): bool
    {
        return $this->update([
            'status' => 'processing',
            'worker_id' => $workerId,
            'started_at' => now(),
        ]);
    }

    public function markAsCompleted(string $domContent): bool
    {
        return $this->update([
            'status' => 'completed',
            'dom_content' => $domContent,
            'completed_at' => now(),
        ]);
    }

    public function markAsFailed(string $errorMessage): bool
    {
        return $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'completed_at' => now(),
        ]);
    }
}
