<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->boolean('marked_for_cancellation')->default(false)->after('status');
            $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete()->after('marked_for_cancellation');
            $table->timestamp('marked_at')->nullable()->after('marked_by');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('marked_for_cancellation');
            $table->dropForeign(['marked_by']);
            $table->dropColumn('marked_by');
            $table->dropColumn('marked_at');
        });
    }
};
