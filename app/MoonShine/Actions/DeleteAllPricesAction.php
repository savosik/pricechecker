<?php

declare(strict_types=1);

namespace App\MoonShine\Actions;

use App\Models\PriceHistory;
use MoonShine\UI\Components\ActionButton;
use MoonShine\Laravel\Actions\Action;

class DeleteAllPricesAction extends Action
{
    public function handle(): mixed
    {
        PriceHistory::truncate();

        return null;
    }
}
