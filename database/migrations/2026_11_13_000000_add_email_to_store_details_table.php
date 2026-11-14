<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('store_details', 'email')) {
            Schema::table('store_details', function (Blueprint $table) {
                $table->string('email', 150)->nullable()->after('contact_number');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('store_details', 'email')) {
            Schema::table('store_details', function (Blueprint $table) {
                $table->dropColumn('email');
            });
        }
    }
};
