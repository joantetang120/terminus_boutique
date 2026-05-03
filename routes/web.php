<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
// Auth
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/', [AuthController::class, 'login']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset (guest only)
Route::middleware(['guest'])->group(function () {
    Route::get('/mot-de-passe-oublie', [ResetPasswordController::class, 'showForgot'])->name('password.forgot');
    Route::post('/mot-de-passe-oublie', [ResetPasswordController::class, 'sendCode'])->name('password.send');
    Route::get('/verification-code', [ResetPasswordController::class, 'showVerify'])->name('password.verify');
    Route::post('/verification-code', [ResetPasswordController::class, 'verifyCode'])->name('password.verify.post');
    Route::post('/verification-code/renvoyer', [ResetPasswordController::class, 'resendCode'])->name('password.resend');
    Route::get('/nouveau-mot-de-passe', [ResetPasswordController::class, 'showReset'])->name('password.reset');
    Route::post('/nouveau-mot-de-passe', [ResetPasswordController::class, 'resetPassword'])->name('password.update');
});

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Factures
    Route::resource('factures', \App\Http\Controllers\FactureController::class);
    Route::post('factures/{facture}/cancel', [\App\Http\Controllers\FactureController::class, 'cancel'])
        ->name('factures.cancel')->middleware('can:facture.cancel');
    Route::post('factures/{facture}/mark-for-cancellation', [\App\Http\Controllers\FactureController::class, 'markForCancellation'])
        ->name('factures.mark-for-cancellation');
    Route::get('factures/{facture}/print', [\App\Http\Controllers\FactureController::class, 'print'])
        ->name('factures.print')->middleware('can:facture.print');
    Route::get('factures/{facture}/preview', [\App\Http\Controllers\FactureController::class, 'preview'])
        ->name('factures.preview')->middleware('can:facture.print');

    // Stock - Groupe préfixé avec middleware can
    Route::prefix('stock')->middleware('can:stock.view')->group(function () {
        Route::get('history', [\App\Http\Controllers\StockController::class, 'index'])->name('stock.index');
        Route::post('entry', [\App\Http\Controllers\StockController::class, 'entree'])
            ->name('stock.entree')->middleware('can:stock.create');
        Route::post('exit', [\App\Http\Controllers\StockController::class, 'sortie'])
            ->name('stock.sortie')->middleware('can:stock.create');
        Route::post('{mouvement}/cancel', [\App\Http\Controllers\StockController::class, 'annuler'])
            ->name('stock.annuler')->middleware('can:stock.cancel');
    });

    // Produits - CRUD complet avec permissions
    Route::prefix('products')->middleware('can:product.view')->group(function () {
        Route::get('/', [\App\Http\Controllers\ProduitController::class, 'index'])->name('produits.index');
        Route::get('create', [\App\Http\Controllers\ProduitController::class, 'create'])
            ->name('produits.create')->middleware('can:product.create');
        Route::post('/', [\App\Http\Controllers\ProduitController::class, 'store'])
            ->name('produits.store')->middleware('can:product.create');
        Route::get('{produit}', [\App\Http\Controllers\ProduitController::class, 'show'])
            ->name('produits.show');
        Route::get('{produit}/edit', [\App\Http\Controllers\ProduitController::class, 'edit'])
            ->name('produits.edit')->middleware('can:product.edit');
        Route::put('{produit}', [\App\Http\Controllers\ProduitController::class, 'update'])
            ->name('produits.update')->middleware('can:product.edit');
        Route::delete('{produit}', [\App\Http\Controllers\ProduitController::class, 'destroy'])
            ->name('produits.destroy')->middleware('can:product.edit');
    });

    // Ghost Invoices - Password protected access
    Route::prefix('ghost-invoices')->middleware('can:ghost.view')->group(function () {
        // Password verification routes (no ghost.session required)
        Route::get('/access', [\App\Http\Controllers\GhostInvoiceController::class, 'passwordForm'])
            ->name('ghost.password');
        Route::post('/access', [\App\Http\Controllers\GhostInvoiceController::class, 'verifyPassword'])
            ->name('ghost.verify');
        Route::post('/logout', [\App\Http\Controllers\GhostInvoiceController::class, 'logout'])
            ->name('ghost.logout');

        // Protected routes (require ghost.session)
        Route::middleware([\App\Http\Middleware\GhostAccessMiddleware::class])->group(function () {
            Route::get('/', [\App\Http\Controllers\GhostInvoiceController::class, 'index'])
                ->name('ghost.index');
            Route::get('/{ghostInvoice}', [\App\Http\Controllers\GhostInvoiceController::class, 'show'])
                ->name('ghost.show');
            Route::get('/{ghostInvoice}/print', [\App\Http\Controllers\GhostInvoiceController::class, 'print'])
                ->name('ghost.print');
        });
    });

    // Comptabilité
    Route::get('comptabilite', [\App\Http\Controllers\ComptaController::class, 'index'])->name('comptabilite.index');
    Route::post('comptabilite', [\App\Http\Controllers\ComptaController::class, 'store'])->name('comptabilite.store');
    Route::put('comptabilite/{entry}', [\App\Http\Controllers\ComptaController::class, 'update'])->name('comptabilite.update');
    Route::post('comptabilite/modifications/{modification}/approuver', [\App\Http\Controllers\ComptaController::class, 'approuver'])
        ->name('compta.approuver')->middleware('can:compta.approve');
    Route::post('comptabilite/modifications/{modification}/rejeter', [\App\Http\Controllers\ComptaController::class, 'rejeter'])
        ->name('compta.rejeter')->middleware('can:compta.approve');

    // Comptabilité des factures (Invoice Accounting)
    Route::get('comptabilite/factures', [\App\Http\Controllers\ComptaFactureController::class, 'index'])
        ->name('comptabilite.factures.index')
        ->middleware('can:compta.view');
    Route::post('comptabilite/factures/paiement', [\App\Http\Controllers\ComptaFactureController::class, 'recordPayment'])
        ->name('comptabilite.factures.payment')
        ->middleware('can:facture.payment');

    // Dépenses (Expenses) - Opérationnelles
    Route::get('depenses', [\App\Http\Controllers\ExpenseController::class, 'index'])
        ->name('expenses.index')
        ->middleware('can:compta.view');
    Route::post('depenses', [\App\Http\Controllers\ExpenseController::class, 'store'])
        ->name('expenses.store')
        ->middleware('can:compta.view');
    Route::put('depenses/{expense}', [\App\Http\Controllers\ExpenseController::class, 'update'])
        ->name('expenses.update')
        ->middleware('can:compta.view');
    Route::delete('depenses/{expense}', [\App\Http\Controllers\ExpenseController::class, 'destroy'])
        ->name('expenses.destroy')
        ->middleware('can:compta.approve');

    // Utilisateurs
    Route::resource('utilisateurs', \App\Http\Controllers\UserController::class)->middleware('can:user.view');
    Route::patch('utilisateurs/{utilisateur}/toggle-status', [\App\Http\Controllers\UserController::class, 'toggleStatus'])
        ->name('utilisateurs.toggle-status');

    // Audit
    Route::get('audit', [\App\Http\Controllers\AuditController::class, 'index'])
        ->name('audit.index')->middleware('can:audit.view');
        
    // Profile
    Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'show'])
        ->name('profile.show');

    Route::post('/profile/request-update', [\App\Http\Controllers\ProfileController::class, 'requestUpdate'])
    ->name('profile.request_update');

    Route::get('/profile/verify', [ProfileController::class, 'verifyForm'])->name('profile.verify_form');

Route::post('/profile/confirm-update', [ProfileController::class, 'confirmUpdate'])->name('profile.confirm_update');

// Report Export
Route::get('/export-data', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/test-pdf', function () {
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML('<h1>Test PDF</h1><p>Ca marche !</p>');
    return $pdf->download('test.pdf');
});

Route::get('/export-pdf', [\App\Http\Controllers\ReportController::class, 'exportPdf'])
    ->name('reports.export.pdf')
    ->middleware('can:compta.view');
});

