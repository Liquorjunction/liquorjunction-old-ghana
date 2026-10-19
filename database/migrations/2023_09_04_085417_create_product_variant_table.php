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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('product_id')->nullable()->default(null);
            $table->bigInteger('variant_size')->nullable()->default(null);
            $table->integer('variant_uof')->nullable()->default(null);
            $table->decimal('variant_price')->nullable()->default(null);
            $table->decimal('variant_discounted_price')->nullable()->default(null);
            $table->integer('variant_qty')->nullable()->default(null);
            $table->string('packets')->nullable()->default(null);
            $table->string('comment')->nullable()->default(null);
            $table->tinyInteger('status')->nullable()->default(1);
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
        Schema::dropIfExists('product_variants');
    }
};
