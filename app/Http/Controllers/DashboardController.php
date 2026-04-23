<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\AccountingEntry;
use App\Services\DueDateAlertService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Mark overdue invoices on dashboard load
        DueDateAlertService::markOverdue();

        $today = today();

        $invoicesToday = Invoice::whereDate('created_at', $today)->count();
        $invoicesYesterday = Invoice::whereDate('created_at', $today->copy()->subDay())->count();
        $invoiceTrend = $invoicesToday - $invoicesYesterday;

        $lowStock = Product::whereColumn('current_stock', '<=', 'alert_threshold')
            ->where('is_active', true)
            ->count();
        $lowStockProducts = Product::whereColumn('current_stock', '<=', 'alert_threshold')
            ->where('is_active', true)
            ->orderByRaw('current_stock - alert_threshold ASC')
            ->limit(5)
            ->get();

        $totalProducts = Product::where('is_active', true)->count();

        $todayRevenue = AccountingEntry::whereDate('date', $today)
            ->where('type', 'recette')
            ->where('status', 'active')
            ->sum('amount');

        $todayExpenses = AccountingEntry::whereDate('date', $today)
            ->where('type', 'depense')
            ->where('status', 'active')
            ->sum('amount');

        $recentInvoices = Invoice::with(['createdBy', 'cancelledBy'])
            ->latest()
            ->limit(5)
            ->get();

        $unpaidInvoices = Invoice::whereIn('status', ['IMPAYEE', 'PARTIELLE', 'EN_RETARD'])
            ->where('balance', '>', 0)
            ->with('createdBy')
            ->latest()
            ->get();

        // Invoice stats for dashboard widget
        $todayTotal = Invoice::whereDate('created_at', $today)
            ->whereNotIn('status', ['ANNULEE'])
            ->sum('total');

        $unpaidCount = Invoice::whereIn('status', ['IMPAYEE', 'PARTIELLE', 'EN_RETARD'])
            ->where('balance', '>', 0)
            ->count();

        $unpaidBalance = Invoice::whereIn('status', ['IMPAYEE', 'PARTIELLE', 'EN_RETARD'])
            ->sum('balance');

        $overdueCount = Invoice::where('status', 'EN_RETARD')
            ->count();

        $alertCount = Invoice::where('status', '!=', 'ANNULEE')
            ->where('due_date', '<=', $today->copy()->addDays(3))
            ->where('due_date', '>=', $today)
            ->where('balance', '>', 0)
            ->count();

        $invoiceStats = [
            'today_count' => $invoicesToday,
            'today_total' => $todayTotal,
            'unpaid_count' => $unpaidCount,
            'unpaid_balance' => $unpaidBalance,
            'overdue_count' => $overdueCount,
            'alert_count' => $alertCount,
        ];

        return view('dashboard.index', compact(
            'invoicesToday',
            'invoiceTrend',
            'lowStock',
            'lowStockProducts',
            'totalProducts',
            'todayRevenue',
            'todayExpenses',
            'recentInvoices',
            'unpaidInvoices',
            'invoiceStats'
        ));
    }
}
