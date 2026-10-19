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
            $table->string('mail_driver')->after('support_email')->nullable();
            $table->string('mail_host')->after('mail_driver')->nullable();
            $table->string('mail_port')->after('mail_host')->nullable();
            $table->string('mail_username')->after('mail_port')->nullable();
            $table->string('mail_password')->after('mail_username')->nullable();
            $table->string('mail_encryption')->after('mail_password')->nullable();
            $table->string('mail_no_replay')->after('mail_encryption')->nullable();
            $table->string('mail_title')->after('mail_no_replay')->nullable();
            $table->text('mail_template')->after('mail_title')->nullable();
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
            $table->dropColumn('mail_driver');
            $table->dropColumn('mail_host');
            $table->dropColumn('mail_port');
            $table->dropColumn('mail_username');
            $table->dropColumn('mail_password');
            $table->dropColumn('mail_encryption');
            $table->dropColumn('mail_no_replay');
            $table->dropColumn('mail_title');
            $table->dropColumn('mail_template');
        });
    }
};
