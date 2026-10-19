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
        Schema::create('class', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('class_type')->nullable();
            $table->integer('dance_category_id')->nullable();
            $table->string('class_name')->nullable();
            $table->string('class_description')->nullable();
            $table->string('class_thumbnail_image')->nullable();
            $table->string('instruction_video')->nullable();
            $table->integer('dance_level')->default(1)->comment('1 => BEGINNER, 2 => INTERMEDIATE, 3 => EXPERT');
            $table->string('duration')->nullable();
            $table->integer('favourite')->nullable();
            $table->string('avg_rating')->nullable();
            $table->string('price')->nullable();
            $table->string('discount')->nullable();
            $table->string('total_time_purchased')->nullable();
            $table->integer('status')->default(0)->comment('1 => APPROVED, 0 => PENDING, 2 => REJECTED');
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
        Schema::dropIfExists('class');
    }
};
