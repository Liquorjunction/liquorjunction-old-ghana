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
        if (Schema::hasTable('settings') && !Schema::hasColumn('settings', 'guest_daily_order_limit')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->unsignedInteger('guest_daily_order_limit')->default(0)->after('delivery_fee')->comment('0 = Unlimited, >0 = Max orders a guest can place per day');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('settings') && Schema::hasColumn('settings', 'guest_daily_order_limit')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('guest_daily_order_limit');
            });
        }
    }
};
