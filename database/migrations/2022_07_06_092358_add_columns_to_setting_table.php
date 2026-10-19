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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('commission_in_per')->after('address')->nullable();
            $table->string('popular_dance_category')->after('commission_in_per')->nullable();
            $table->string('popular_dance_class')->after('popular_dance_category')->nullable();
            $table->string('popular_instructor')->after('popular_dance_class')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('commission_in_per');
            $table->dropColumn('popular_dance_category');
            $table->dropColumn('popular_dance_class');
            $table->dropColumn('popular_instructor');
        });
    }
};
