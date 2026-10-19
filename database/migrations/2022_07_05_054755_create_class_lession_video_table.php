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
        Schema::create('class_lession_video', function (Blueprint $table) {
            $table->id();
            $table->integer('class_lession_id')->nullable();
            $table->string('video_name')->nullable();
            $table->string('video_file')->nullable();
            $table->integer('status')->default(1)->comment('1 => ACTIVE, 0 => INACTIVE, 2 => DELETE');
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
        Schema::dropIfExists('class_lession_video');
    }
};
