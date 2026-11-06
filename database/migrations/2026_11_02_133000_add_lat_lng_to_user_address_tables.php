<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('user_address')) {
            Schema::table('user_address', function (Blueprint $table) {
                if (!Schema::hasColumn('user_address', 'latitude')) {
                    $table->decimal('latitude', 10, 8)->nullable()->after('zip_code');
                }
                if (!Schema::hasColumn('user_address', 'longitude')) {
                    $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
                }
            });
        }

        if (Schema::hasTable('user_bill_address')) {
            Schema::table('user_bill_address', function (Blueprint $table) {
                if (!Schema::hasColumn('user_bill_address', 'latitude')) {
                    $table->decimal('latitude', 10, 8)->nullable()->after('zip_code');
                }
                if (!Schema::hasColumn('user_bill_address', 'longitude')) {
                    $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('user_address')) {
            Schema::table('user_address', function (Blueprint $table) {
                if (Schema::hasColumn('user_address', 'longitude')) {
                    $table->dropColumn('longitude');
                }
                if (Schema::hasColumn('user_address', 'latitude')) {
                    $table->dropColumn('latitude');
                }
            });
        }

        if (Schema::hasTable('user_bill_address')) {
            Schema::table('user_bill_address', function (Blueprint $table) {
                if (Schema::hasColumn('user_bill_address', 'longitude')) {
                    $table->dropColumn('longitude');
                }
                if (Schema::hasColumn('user_bill_address', 'latitude')) {
                    $table->dropColumn('latitude');
                }
            });
        }
    }
};
