<?php

namespace App\Http\Controllers;

use App\Models\PriceHistory;
use Illuminate\Http\Request;
use MoonShine\Laravel\MoonShineUI;

class PriceHistoryController extends Controller
{
    public function deleteAll()
    {
        PriceHistory::truncate();

        MoonShineUI::toast('Все записи удалены', 'success');

        return back();
    }
}
