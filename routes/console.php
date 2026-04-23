<?php

use App\Services\DueDateAlertService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduled job: Mark overdue invoices daily at 8:00 AM
Schedule::call(function () {
    $count = DueDateAlertService::markOverdue();
    \Log::info("DueDateAlertService: {$count} facture(s) passée(s) en retard.");
})->dailyAt('08:00')->name('mark-overdue-invoices');

// Console command to manually trigger overdue check
Artisan::command('invoices:mark-overdue', function () {
    $count = DueDateAlertService::markOverdue();
    $this->info("{$count} facture(s) passée(s) en retard.");
})->purpose('Marquer les factures en retard comme EN_RETARD');
