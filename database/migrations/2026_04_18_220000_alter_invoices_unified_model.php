<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Supprimer le champ type devenu inutile
            $table->dropColumn('type');

            // Renommer advance_amount en paid_amount
            $table->renameColumn('advance_amount', 'paid_amount');

            // Supprimer le champ note (non présent dans le nouveau modèle)
            $table->dropColumn('note');

            // Modifier le enum status
            $table->enum('status', ['IMPAYEE', 'PARTIELLE', 'SOLDEE', 'ANNULEE'])
                ->default('IMPAYEE')
                ->change();

            // Ajouter due_date (automatique: created_at + 10 jours)
            $table->date('due_date')->nullable()->after('balance');

            // Ajouter soft deletes
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('type', ['comptant', 'credit', 'avance'])->after('number');
            $table->renameColumn('paid_amount', 'advance_amount');
            $table->text('note')->nullable()->after('balance');
            $table->enum('status', ['payee', 'credit', 'avance', 'annulee'])->change();
            $table->dropColumn('due_date');
            $table->dropSoftDeletes();
        });
    }
};
