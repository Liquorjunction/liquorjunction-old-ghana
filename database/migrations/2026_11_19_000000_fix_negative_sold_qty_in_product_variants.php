<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix all variants where sold_qty went negative due to unpaid cancellation bug
        DB::table('product_variants')
            ->where('sold_qty', '<', 0)
            ->update([
                'available_qty' => DB::raw('variant_qty'),
                'sold_qty' => 0,
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data cleanup migration: rollback is not required as negative stock was invalid.
    }
};
