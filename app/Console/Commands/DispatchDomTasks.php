<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\DomTask;
use App\Models\Marketplace;
use App\Models\ProductLink;
use Illuminate\Console\Command;

class DispatchDomTasks extends Command
{
    protected $signature = 'dom:dispatch 
        {--marketplace= : Marketplace code (ozon, wildberries)}
        {--limit=100 : Maximum number of tasks to create}
        {--product-link= : Specific product link ID}';

    protected $description = 'Create DOM parsing tasks for product links';

    public function handle(): int
    {
        $marketplaceCode = $this->option('marketplace') ?: 'ozon';
        $limit = (int) $this->option('limit');
        $productLinkId = $this->option('product-link');

        // Find marketplace
        $marketplace = Marketplace::where('code', $marketplaceCode)->first();
        if (!$marketplace) {
            $this->error("Marketplace not found: {$marketplaceCode}");
            return self::FAILURE;
        }

        // Build query
        $query = ProductLink::where('marketplace_id', $marketplace->id);
        
        if ($productLinkId) {
            $query->where('id', $productLinkId);
        }
        
        $productLinks = $query->limit($limit)->get();

        if ($productLinks->isEmpty()) {
            $this->warn('No product links found');
            return self::SUCCESS;
        }

        $created = 0;
        foreach ($productLinks as $productLink) {
            // Check if pending task already exists
            $exists = DomTask::where('product_link_id', $productLink->id)
                ->where('status', 'pending')
                ->exists();
            
            if ($exists) {
                $this->line("Skipping {$productLink->url} - pending task exists");
                continue;
            }

            DomTask::create([
                'product_link_id' => $productLink->id,
                'url' => $productLink->url,
                'marketplace_code' => $marketplaceCode,
                'status' => 'pending',
            ]);
            
            $created++;
            $this->info("Created task for: {$productLink->url}");
        }

        $this->info("Created {$created} DOM parsing tasks");
        return self::SUCCESS;
    }
}
