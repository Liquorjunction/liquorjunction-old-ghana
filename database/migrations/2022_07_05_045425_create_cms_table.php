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
        Schema::create('cms', function (Blueprint $table) {
            $table->id();
            $table->integer('language_id')->nullable();
            $table->string('page_name')->nullable();
            $table->text('page_content')->nullable();
            $table->string('title')->nullable();
            $table->string('body')->nullable();
            $table->string('image')->nullable();
            $table->integer('create_by')->nullable();
            $table->integer('update_by')->nullable();
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
        Schema::dropIfExists('cms');
    }
};
