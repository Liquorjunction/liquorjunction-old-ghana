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
        Schema::create('class_purchase_history', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('class_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('total_amount')->nullable();
            $table->string('admin_commission_amount')->nullable();
            $table->string('instructor_amount')->nullable();
            $table->integer('status')->default(1)->comment('1 => SUCCESS, 2 => FAILED');
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
        Schema::dropIfExists('class_purchase_history');
    }
};
