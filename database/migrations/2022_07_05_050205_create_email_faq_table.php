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
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->integer('language_id')->nullable();
            $table->string('question_name')->nullable();
            $table->text('answer')->nullable();
            $table->string('create_by')->nullable();
            $table->string('update_by')->nullable();
            $table->integer('status')->default(1)->comment('1 => ACTIVE, 0 => INACTIVE, 2 => DELETE');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('faqs');
    }
};
