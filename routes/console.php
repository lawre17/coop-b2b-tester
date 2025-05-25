<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');


Schedule::call(function () {
    Artisan::call('simulate:bank-b2b');
})->everyThirtySeconds()->name('simulation')->withoutOverlapping()->onFailure(function () {
    // Handle failure, e.g., log an error or send a notification
    Log::error('Bank B2B simulation failed.');
});
