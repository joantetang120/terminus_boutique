<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\AccountingEntry;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
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

        $unpaidInvoices = Invoice::whereIn('status', ['credit', 'avance'])
            ->where('balance', '>', 0)
            ->with('createdBy')
            ->latest()
            ->get();

        return view('dashboard.index', compact(
            'invoicesToday',
            'invoiceTrend',
            'lowStock',
            'lowStockProducts',
            'totalProducts',
            'todayRevenue',
            'todayExpenses',
            'recentInvoices',
            'unpaidInvoices'
        ));
    }
}
