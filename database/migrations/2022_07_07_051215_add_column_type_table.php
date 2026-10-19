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
            $table->boolean('popular_dance_category')->default(0)->change();
            $table->boolean('popular_dance_class')->default(0)->change();
            $table->boolean('popular_instructor')->default(0)->change();
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
            $table->dropColumn('popular_dance_category');
            $table->dropColumn('popular_dance_class');
            $table->dropColumn('popular_instructor');
        });
    }
};
