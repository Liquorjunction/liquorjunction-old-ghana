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
        Schema::create('withdraw_history', function (Blueprint $table) {
            $table->id();
            $table->integer('instructor_id')->nullable();
            $table->string('amount')->nullable();
            $table->integer('status')->default(0)->comment('0 => REQUESTED, 1 => PAID, 2 => CANCELLED');
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
        Schema::dropIfExists('withdraw_history');
    }
};
