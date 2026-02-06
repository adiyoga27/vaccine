<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule Vaccine Notification
\Illuminate\Support\Facades\Schedule::call(function () {
    \Illuminate\Support\Facades\Artisan::call('vaccine:send-daily-reminders');
})
->dailyAt('12:03')
->timezone('Asia/Makassar');
