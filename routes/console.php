<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('reservations:remind')->everyMinute();
Schedule::command('instagram:sync')->everyFifteenMinutes();
Schedule::command('reservations:prune-proofs')->dailyAt('02:30');
