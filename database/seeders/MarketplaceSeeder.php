<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Marketplace;

class MarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        Marketplace::firstOrCreate(['code' => 'ozon'], ['name' => 'Ozon', 'color' => '#005bff']);
        Marketplace::firstOrCreate(['code' => 'wildberries'], ['name' => 'Wildberries', 'color' => '#cb11ab']);
    }
}
