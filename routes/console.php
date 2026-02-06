<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule Vaccine Notification
\Illuminate\Support\Facades\Schedule::command('vaccine:send-daily-reminders')
    ->dailyAt('11:30')
    ->timezone('Asia/Makassar');
