<?php

namespace App\Http\Controllers;

use App\Models\AccountingEntry;
use App\Models\AccountingModification;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComptaController extends Controller
{
    public function index(Request $request)
    {
        $filterDate = $request->filled('date') ? $request->date : today()->format('Y-m-d');
        $filterLabel = $request->filled('date') ? \Carbon\Carbon::parse($request->date)->format('d/m/Y') : "du jour";

        // Overall totals
        $totalRecettes = AccountingEntry::where('type', 'recette')->where('status', 'active')->sum('amount');

        // Manual expenses (Expense reference or no reference)
        $totalDepenses = AccountingEntry::where('type', 'depense')->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('reference_type')
                  ->orWhere('reference_type', 'Expense');
            })
            ->sum('amount');

        // Product expenses (stock movements)
        $totalDepensesProduits = AccountingEntry::where('type', 'depense')->where('status', 'active')
            ->where('reference_type', StockMovement::class)
            ->sum('amount');

        $totalSoldeNet = $totalRecettes - $totalDepenses;

        // Filtered date totals (use request date or today)
        $todayRecettes = AccountingEntry::whereDate('date', $filterDate)
            ->where('type', 'recette')->where('status', 'active')->sum('amount');
        $todayDepenses = AccountingEntry::whereDate('date', $filterDate)
            ->where('type', 'depense')->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('reference_type')
                  ->orWhere('reference_type', 'Expense');
            })
            ->sum('amount');
        $todayDepensesProduits = AccountingEntry::whereDate('date', $filterDate)
            ->where('type', 'depense')->where('status', 'active')
            ->where('reference_type', StockMovement::class)
            ->sum('amount');
        $soldeNet = $todayRecettes - $todayDepenses;

        $tab = $request->get('tab', 'entries');

        $entriesQuery = AccountingEntry::with('createdBy');
        if ($request->filled('type')) {
            $entriesQuery->where('type', $request->type);
        }
        if ($request->filled('date')) {
            $entriesQuery->whereDate('date', $request->date);
        }
        $entries = $entriesQuery->latest()->paginate(20);

        $modifications = AccountingModification::with(['entry', 'requestedBy', 'reviewedBy'])
            ->latest()
            ->paginate(20);

        $pendingCount = AccountingModification::where('status', 'pending')->count();

        return view('comptabilite.index', compact(
            'totalRecettes', 'totalDepenses', 'totalDepensesProduits', 'totalSoldeNet',
            'todayRecettes', 'todayDepenses', 'todayDepensesProduits', 'soldeNet',
            'filterLabel', 'entries', 'modifications', 'pendingCount', 'tab'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:recette,depense',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|min:5',
            'date' => 'required|date',
        ]);

        AccountingEntry::create([
            'date' => $validated['date'],
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'status' => 'active',
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'Écriture enregistrée.');
    }

    public function update(Request $request, AccountingEntry $entry)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|min:5',
        ]);

        // If admin, direct update; else create modification request
        if (Auth::user()->can('compta.approve')) {
            $entry->update($validated);
            return back()->with('success', 'Écriture modifiée.');
        }

        // Create modification request
        AccountingModification::create([
            'entry_id' => $entry->id,
            'requested_by' => Auth::id(),
            'requested_at' => now(),
            'old_values' => ['amount' => $entry->amount, 'description' => $entry->description],
            'new_values' => $validated,
            'status' => 'pending',
        ]);

        $entry->update(['status' => 'pending_modification']);

        return back()->with('success', 'Demande de modification soumise pour approbation.');
    }

    public function approuver(AccountingModification $modification)
    {
        $this->authorize('compta.approve');

        DB::transaction(function () use ($modification) {
            $modification->entry->update($modification->new_values);
            $modification->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            $modification->entry->update(['status' => 'active']);
        });

        return back()->with('success', 'Modification approuvée.');
    }

    public function rejeter(Request $request, AccountingModification $modification)
    {
        $this->authorize('compta.approve');

        $validated = $request->validate([
            'review_note' => 'required|string|min:5',
        ]);

        $modification->update([
            'status' => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'review_note' => $validated['review_note'],
        ]);

        $modification->entry->update(['status' => 'active']);

        return back()->with('success', 'Modification rejetée.');
    }
}
