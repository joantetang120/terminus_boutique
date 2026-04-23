<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('ghost_division_coefficient', 10, 2)->default(2.0)->after('is_active');
            $table->string('ghost_access_password')->nullable()->after('ghost_division_coefficient');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ghost_division_coefficient');
            $table->dropColumn('ghost_access_password');
        });
    }
};
