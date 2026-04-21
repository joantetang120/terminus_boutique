<?php

namespace App\Services;

use App\Models\Invoice;
use Carbon\Carbon;

class DueDateAlertService
{
    /**
     * Get due date alerts - returns two collections
     *
     * (1) Upcoming alerts: due_date between today and today+3 days, status != SOLDEE && != ANNULEE
     * (2) Overdue alerts: due_date < today, status != SOLDEE && != ANNULEE
     *
     * @return array{upcoming: \Illuminate\Support\Collection, overdue: \Illuminate\Support\Collection}
     */
    public static function getAlerts(): array
    {
        $today = Carbon::today();
        $inThreeDays = Carbon::today()->addDays(3);

        // (1) Upcoming alerts: J-3 (alert at J+7 after creation since due_date = J+10)
        $upcoming = Invoice::whereBetween('due_date', [$today, $inThreeDays])
            ->whereNotIn('status', ['SOLDEE', 'ANNULEE'])
            ->orderBy('due_date', 'asc')
            ->get();

        // (2) Overdue alerts: due_date < today
        $overdue = Invoice::whereDate('due_date', '<', $today)
            ->whereNotIn('status', ['SOLDEE', 'ANNULEE'])
            ->orderBy('due_date', 'asc')
            ->get();

        return [
            'upcoming' => $upcoming,
            'overdue' => $overdue,
        ];
    }

    /**
     * Mark overdue invoices as EN_RETARD
     *
     * Updates status to EN_RETARD for invoices where due_date < today
     * and status is not SOLDEE or ANNULEE.
     *
     * Call this on dashboard load or via scheduled job (php artisan schedule:run)
     *
     * @return int Number of invoices updated
     */
    public static function markOverdue(): int
    {
        $today = Carbon::today();

        $overdueInvoices = Invoice::whereDate('due_date', '<', $today)
            ->whereNotIn('status', ['SOLDEE', 'ANNULEE', 'EN_RETARD'])
            ->get();

        $count = 0;
        foreach ($overdueInvoices as $invoice) {
            $invoice->update(['status' => 'EN_RETARD']);
            $count++;

            // Log the status change
            activity('invoice_overdue')
                ->performedOn($invoice)
                ->withProperties([
                    'invoice_number' => $invoice->number,
                    'client_name' => $invoice->client_name,
                    'due_date' => $invoice->due_date->format('Y-m-d'),
                    'previous_status' => $invoice->getOriginal('status') ?? 'unknown',
                    'new_status' => 'EN_RETARD',
                    'days_overdue' => $today->diffInDays($invoice->due_date),
                ])
                ->log('Facture passée en retard: ' . $invoice->number . ' (échéance: ' . $invoice->due_date->format('d/m/Y') . ')');
        }

        return $count;
    }
}
