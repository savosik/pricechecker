<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\ParseProductPriceJob;
use App\Models\Product;
use Illuminate\Console\Command;

class DispatchPriceParsingJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch jobs to parse product prices from configured marketplaces';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Starting price parsing dispatch...');

        \App\Models\ProductLink::chunk(100, function ($links) {
            foreach ($links as $link) {
                if (empty($link->marketplace_id) || empty($link->url)) {
                    continue;
                }

                $marketplace = $link->marketplace;
                $queue = $marketplace ? strtolower($marketplace->code ?: $marketplace->name) : 'default';

                $this->info("Dispatching job for Link ID: {$link->id}, Marketplace: {$queue}");

                ParseProductPriceJob::dispatch($link)->onQueue($queue);
            }
        });

        $this->info('All jobs dispatched.');
    }
}
