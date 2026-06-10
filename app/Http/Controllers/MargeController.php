<?php

namespace App\Http\Controllers;

use App\Models\InvoiceItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MargeController extends Controller
{
    public function index(Request $request)
    {
        $costSub = '(SELECT COALESCE(sm.total_cost, 0) FROM stock_movements sm'
            . ' WHERE sm.reference_type = "invoice"'
            . ' AND sm.reference_id = invoice_items.invoice_id'
            . ' AND sm.product_id = invoice_items.product_id'
            . ' AND sm.type = "exit"'
            . ' AND sm.deleted_at IS NULL'
            . ' ORDER BY sm.id ASC LIMIT 1)';

        $baseQuery = InvoiceItem::join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->leftJoin('products', 'products.id', '=', 'invoice_items.product_id')
            ->whereNull('invoices.deleted_at')
            ->where('invoices.status', '!=', 'ANNULEE');

        $baseQuery->when($request->filled('date'), fn($q) => $q->whereDate('invoices.created_at', $request->date))
            ->when($request->filled('month'), fn($q) => $q->whereMonth('invoices.created_at', $request->month))
            ->when($request->filled('year'), fn($q) => $q->whereYear('invoices.created_at', $request->year))
            ->when($request->filled('product_id'), fn($q) => $q->where('invoice_items.product_id', $request->product_id))
            ->when($request->filled('client_name'), fn($q) => $q->where('invoices.client_name', 'like', '%' . $request->client_name . '%'));

        // Items (paginated)
        $items = (clone $baseQuery)->select([
                'invoice_items.*',
                'invoices.number as invoice_number',
                'invoices.client_name as client_name',
                'invoices.created_at as invoice_date',
                'products.name as product_name',
                DB::raw("COALESCE($costSub, 0) as cost"),
            ])
            ->orderBy('invoices.created_at', 'desc')
            ->orderBy('invoice_items.id', 'asc')
            ->paginate(50)
            ->through(function ($item) {
                $item->margin = $item->total_price - $item->cost;
                $item->margin_pct = $item->total_price > 0
                    ? round($item->margin / $item->total_price * 100, 2)
                    : 0;
                return $item;
            });

        // Totals
        $totals = (clone $baseQuery)->select([
                DB::raw('COALESCE(SUM(invoice_items.total_price), 0) as total_ca'),
                DB::raw("COALESCE(SUM($costSub), 0) as total_cost"),
            ])
            ->first();

        $totalCa = (float) $totals->total_ca;
        $totalCost = (float) $totals->total_cost;
        $totalMargin = $totalCa - $totalCost;
        $marginRate = $totalCa > 0 ? round($totalMargin / $totalCa * 100, 2) : 0;

        // Top client by margin
        $topClient = (clone $baseQuery)
            ->select([
                'invoices.client_name',
                DB::raw('SUM(invoice_items.total_price) as total_ca'),
                DB::raw("COALESCE(SUM($costSub), 0) as total_cost"),
            ])
            ->whereNotNull('invoices.client_name')
            ->where('invoices.client_name', '!=', '')
            ->groupBy('invoices.client_name')
            ->orderBy(DB::raw('SUM(invoice_items.total_price) - COALESCE(SUM(' . $costSub . '), 0)'), 'desc')
            ->first();

        if ($topClient) {
            $topCa = (float) $topClient->total_ca;
            $topCost = (float) $topClient->total_cost;
            $topMargin = $topCa - $topCost;
            $topClientPct = $topCa > 0 ? round($topMargin / $topCa * 100, 2) : 0;
            $topClientName = $topClient->client_name;
        } else {
            $topClientName = '—';
            $topMargin = 0;
            $topClientPct = 0;
        }

        // All products for filter dropdown
        $products = Product::orderBy('name')->get(['id', 'name']);

        return view('marge.index', compact(
            'items', 'totalCa', 'totalCost', 'totalMargin', 'marginRate',
            'topClientName', 'topMargin', 'topClientPct', 'products'
        ));
    }
}
