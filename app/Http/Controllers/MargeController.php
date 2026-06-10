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

        // Top client by margin (subquery approach to avoid ONLY_FULL_GROUP_BY)
        $topClientSql = 'SELECT i.client_name, ii.total_price, COALESCE(('
            . 'SELECT sm.total_cost FROM stock_movements sm'
            . ' WHERE sm.reference_type = "invoice"'
            . ' AND sm.reference_id = ii.invoice_id'
            . ' AND sm.product_id = ii.product_id'
            . ' AND sm.type = "exit"'
            . ' AND sm.deleted_at IS NULL'
            . ' ORDER BY sm.id ASC LIMIT 1'
            . '), 0) as cost'
            . ' FROM invoice_items ii'
            . ' JOIN invoices i ON i.id = ii.invoice_id'
            . ' WHERE i.deleted_at IS NULL AND i.status != "ANNULEE"'
            . ' AND i.client_name IS NOT NULL AND i.client_name != ""';

        $bindings = [];
        if ($request->filled('date')) { $topClientSql .= ' AND DATE(i.created_at) = ?'; $bindings[] = $request->date; }
        if ($request->filled('month')) { $topClientSql .= ' AND MONTH(i.created_at) = ?'; $bindings[] = $request->month; }
        if ($request->filled('year')) { $topClientSql .= ' AND YEAR(i.created_at) = ?'; $bindings[] = $request->year; }
        if ($request->filled('product_id')) { $topClientSql .= ' AND ii.product_id = ?'; $bindings[] = $request->product_id; }
        if ($request->filled('client_name')) { $topClientSql .= ' AND i.client_name LIKE ?'; $bindings[] = '%' . $request->client_name . '%'; }

        $topClient = DB::selectOne(
            'SELECT t.client_name, SUM(t.total_price) as total_ca, SUM(t.cost) as total_cost'
            . ' FROM (' . $topClientSql . ') t'
            . ' GROUP BY t.client_name'
            . ' ORDER BY SUM(t.total_price) - SUM(t.cost) DESC'
            . ' LIMIT 1',
            $bindings
        );

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
