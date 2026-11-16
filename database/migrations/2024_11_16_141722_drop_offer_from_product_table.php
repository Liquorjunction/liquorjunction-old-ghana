<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the `offer` column from `product` table if it exists
        Schema::table('product', function (Blueprint $table) {
            if (Schema::hasColumn('product', 'offer')) {
                $table->dropColumn('offer');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add the `offer` column back (string, nullable) – adjust type if you know the original definition
        Schema::table('product', function (Blueprint $table) {
            $table->string('offer')->nullable();
        });
    }
};
?>