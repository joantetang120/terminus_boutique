<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class InvoiceNumberService
{
    /**
     * Generate a unique invoice number in format FAC-YYYYMM-XXXX
     * Uses DB transaction with lockForUpdate() to prevent duplicates
     *
     * @return string
     */
    public static function generate(): string
    {
        return DB::transaction(function () {
            $yearMonth = now()->format('Ym');
            $prefix = 'FAC-' . $yearMonth . '-';

            // Get the last invoice number for current month with lock
            $lastInvoice = Invoice::where('number', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderBy('number', 'desc')
                ->first();

            if ($lastInvoice) {
                // Extract the numeric part from the last number
                $lastNumber = (int) substr($lastInvoice->number, -4);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }

            return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        });
    }
}
