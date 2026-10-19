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
            $table->string('youtube_link')->after('tiktok_link')->nullable();
            $table->string('support_name')->after('youtube_link')->nullable();
            $table->string('support_email')->after('support_name')->nullable();
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
            $table->dropColumn('youtube_link');
            $table->dropColumn('support_name');
            $table->dropColumn('support_email');
        });
    }
};
