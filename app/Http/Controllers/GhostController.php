<?php

namespace App\Http\Controllers;

use App\Models\GhostInvoice;
use Illuminate\Http\Request;

class GhostController extends Controller
{
    public function index(Request $request)
    {
        $query = GhostInvoice::with('createdBy');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('number', 'like', '%' . $request->search . '%')
                  ->orWhere('client_name', 'like', '%' . $request->search . '%');
            });
        }

        $ghostInvoices = $query->latest()->paginate(20);

        return view('ghost.index', compact('ghostInvoices'));
    }
}
