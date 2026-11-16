<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('labels')
            ->where('label_name', 'offers')
            ->update(['label_value' => 'Special Offers']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('labels')
            ->where('label_name', 'offers')
            ->update(['label_value' => 'Best Offers']);
    }
};
