<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            if (!Schema::hasColumn('offers', 'start_date')) {
                $table->date('start_date')->nullable()->after('dis_amount');
            }
        });

        // Backfill existing offers with their created_at date
        DB::statement("UPDATE offers SET start_date = DATE(created_at) WHERE start_date IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            if (Schema::hasColumn('offers', 'start_date')) {
                $table->dropColumn('start_date');
            }
        });
    }
};
