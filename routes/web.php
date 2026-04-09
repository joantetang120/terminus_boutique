<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ResetPasswordController;

// Auth
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
    Route::post('factures/{facture}/annuler', [\App\Http\Controllers\FactureController::class, 'annuler'])
        ->name('factures.annuler')->middleware('can:facture.cancel');

    // Stock
    Route::get('stock', [\App\Http\Controllers\StockController::class, 'index'])->name('stock.index');
    Route::post('stock/entree', [\App\Http\Controllers\StockController::class, 'entree'])->name('stock.entree');
    Route::post('stock/sortie', [\App\Http\Controllers\StockController::class, 'sortie'])->name('stock.sortie');
    Route::post('stock/{mouvement}/annuler', [\App\Http\Controllers\StockController::class, 'annuler'])
        ->name('stock.annuler')->middleware('can:stock.cancel');

    // Produits
    Route::resource('produits', \App\Http\Controllers\ProduitController::class)->only(['index', 'create', 'store']);

    // Ghost
    Route::get('ghost', [\App\Http\Controllers\GhostController::class, 'index'])
        ->name('ghost.index')->middleware('can:ghost.view');

    // Comptabilité
    Route::get('comptabilite', [\App\Http\Controllers\ComptaController::class, 'index'])->name('comptabilite.index');
    Route::post('comptabilite', [\App\Http\Controllers\ComptaController::class, 'store'])->name('comptabilite.store');
    Route::put('comptabilite/{entry}', [\App\Http\Controllers\ComptaController::class, 'update'])->name('comptabilite.update');
    Route::post('comptabilite/modifications/{modification}/approuver', [\App\Http\Controllers\ComptaController::class, 'approuver'])
        ->name('compta.approuver')->middleware('can:compta.approve');
    Route::post('comptabilite/modifications/{modification}/rejeter', [\App\Http\Controllers\ComptaController::class, 'rejeter'])
        ->name('compta.rejeter')->middleware('can:compta.approve');

    // Utilisateurs
    Route::resource('utilisateurs', \App\Http\Controllers\UserController::class)->middleware('can:user.view');
    Route::patch('utilisateurs/{utilisateur}/toggle-status', [\App\Http\Controllers\UserController::class, 'toggleStatus'])
        ->name('utilisateurs.toggle-status');

    // Audit
    Route::get('audit', [\App\Http\Controllers\AuditController::class, 'index'])
        ->name('audit.index')->middleware('can:audit.view');
});
