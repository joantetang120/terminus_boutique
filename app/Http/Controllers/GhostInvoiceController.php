<?php

namespace App\Http\Controllers;

use App\Models\GhostInvoice;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class GhostInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:ghost.view');
    }

    /**
     * Show password form for ghost invoices access
     */
    public function passwordForm()
    {
        return view('ghost.password');
    }

    /**
     * Verify password for ghost invoices access
     */
    public function verifyPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $storedPassword = config('app.ghost_access_password');

        if (!$storedPassword) {
            return redirect()
                ->back()
                ->with('error', 'Aucun mot de passe configuré. Contactez l\'administrateur.');
        }

        if (!Hash::check($request->password, $storedPassword)) {
            return redirect()
                ->back()
                ->with('error', 'Mot de passe incorrect.');
        }

        // Mark session as verified
        Session::put('ghost_access_verified', true);
        Session::put('ghost_access_verified_at', now());

        // Log the access
        activity('ghost.access')
            ->causedBy(Auth::user())
            ->withProperties([
                'action' => 'password_verified',
                'ip' => $request->ip(),
            ])
            ->log('Accès aux factures fantômes autorisé pour ' . Auth::user()->name);

        // Redirect to intended URL or index
        $intendedUrl = Session::pull('ghost_intended_url', route('ghost.index'));
        return redirect($intendedUrl);
    }

    /**
     * Logout from ghost invoices access
     */
    public function logout()
    {
        Session::forget(['ghost_access_verified', 'ghost_access_verified_at']);

        activity('ghost.access')
            ->causedBy(Auth::user())
            ->withProperties(['action' => 'logout'])
            ->log('Déconnexion des factures fantômes par ' . Auth::user()->name);

        return redirect()->route('dashboard')->with('success', 'Déconnecté des factures fantômes.');
    }

    /**
     * Display a paginated list of ghost invoices with filters
     */
    public function index(Request $request)
    {
        $query = GhostInvoice::with(['createdBy']);

        // Filter by search (number or client name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('number', 'like', '%' . $search . '%')
                  ->orWhere('client_name', 'like', '%' . $search . '%');
            });
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Filter by client
        if ($request->filled('client')) {
            $query->where('client_name', 'like', '%' . $request->client . '%');
        }

        $ghostInvoices = $query->latest()->paginate(20);

        // Check which real invoices are cancelled for visual indication
        $realInvoiceIds = $ghostInvoices->pluck('real_invoice_id')->filter();
        $cancelledInvoiceIds = Invoice::whereIn('id', $realInvoiceIds)
            ->where('status', 'ANNULEE')
            ->pluck('id')
            ->toArray();

        // Log the list view
        activity('ghost.view')
            ->causedBy(Auth::user())
            ->withProperties([
                'action' => 'list',
                'filters' => $request->only(['search', 'date', 'client']),
                'count' => $ghostInvoices->count(),
            ])
            ->log('Consultation liste des factures fantômes par ' . Auth::user()->name);

        return view('ghost.index', compact('ghostInvoices', 'cancelledInvoiceIds'));
    }

    /**
     * Display the specified ghost invoice with items
     */
    public function show(GhostInvoice $ghostInvoice)
    {
        $ghostInvoice->load(['items', 'createdBy']);

        // Check if the real invoice is cancelled
        $realInvoiceCancelled = false;
        $realInvoice = null;
        
        if ($ghostInvoice->real_invoice_id) {
            $realInvoice = Invoice::find($ghostInvoice->real_invoice_id);
            $realInvoiceCancelled = $realInvoice && $realInvoice->status === 'ANNULEE';
        }

        // Log the detail view
        activity('ghost.view')
            ->performedOn($ghostInvoice)
            ->causedBy(Auth::user())
            ->withProperties([
                'action' => 'show',
                'ghost_invoice_number' => $ghostInvoice->number,
                'ghost_invoice_id' => $ghostInvoice->id,
                'real_invoice_id' => $ghostInvoice->real_invoice_id,
                'real_invoice_cancelled' => $realInvoiceCancelled,
                'client_name' => $ghostInvoice->client_name,
                'total' => $ghostInvoice->total,
            ])
            ->log('Consultation détail fantôme: ' . $ghostInvoice->number . ' (' . $ghostInvoice->client_name . ') par ' . Auth::user()->name);

        return view('ghost.show', compact('ghostInvoice', 'realInvoiceCancelled', 'realInvoice'));
    }
}
