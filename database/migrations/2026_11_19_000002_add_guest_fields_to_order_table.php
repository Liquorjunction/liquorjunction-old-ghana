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
        Schema::table('order', function (Blueprint $table) {
            if (!Schema::hasColumn('order', 'client_type')) {
                $table->string('client_type', 20)->default('registered')->after('user_id')->index();
            }
            if (!Schema::hasColumn('order', 'guest_user_id')) {
                $table->foreignId('guest_user_id')->nullable()->after('client_type')->constrained('guest_users')->nullOnDelete();
            }
            if (Schema::hasColumn('order', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            if (Schema::hasColumn('order', 'guest_user_id')) {
                $table->dropForeign(['guest_user_id']);
                $table->dropColumn('guest_user_id');
            }
            if (Schema::hasColumn('order', 'client_type')) {
                $table->dropColumn('client_type');
            }
        });
    }
};
