<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('main_users', function (Blueprint $table) {
            $table->integer('is_verify_instructor')->default('0')->comment('1 => PENDING, 2 => APPROVED, 3 => REJECTED')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('main_users', function (Blueprint $table) {
            $table->dropColumn('is_verify_instructor');
        });
    }
};
