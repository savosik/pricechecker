<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('parse:prices')->twiceDaily(0, 12);
Schedule::command('dom-tasks:clean --days=3')->dailyAt('03:00');
