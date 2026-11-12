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
        // 1. Table `order`: Core fields used for search, sort & filters
        Schema::table('order', function (Blueprint $table) {
            $table->index('order_id', 'idx_order_order_id');
            $table->index('uniqid', 'idx_order_uniqid');
            $table->index('status', 'idx_order_status');
            $table->index('order_status', 'idx_order_order_status');
            $table->index('order_type', 'idx_order_order_type');
            $table->index('order_date', 'idx_order_order_date');
            $table->index('created_at', 'idx_order_created_at');
            $table->index('supplier_id', 'idx_order_supplier_id');
            $table->index(['status', 'order_status', 'created_at'], 'idx_order_status_order_status_created');
            $table->index(['user_id', 'created_at'], 'idx_order_user_id_created_at');
        });

        // 2. Table `order_info`: Joined on order.id in every order query
        Schema::table('order_info', function (Blueprint $table) {
            $table->index('order_id', 'idx_order_info_order_id');
        });

        // 3. Table `transactions`: Joined on order.id in every order query
        Schema::table('transactions', function (Blueprint $table) {
            $table->index('order_id', 'idx_transactions_order_id');
        });

        // 4. Table `order_detail`: Joined for order items & reporting
        Schema::table('order_detail', function (Blueprint $table) {
            $table->index('order_id', 'idx_order_detail_order_id');
            $table->index('product_id', 'idx_order_detail_product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 1. Table `order` indexes rollback
        Schema::table('order', function (Blueprint $table) {
            $table->dropIndex('idx_order_order_id');
            $table->dropIndex('idx_order_uniqid');
            $table->dropIndex('idx_order_status');
            $table->dropIndex('idx_order_order_status');
            $table->dropIndex('idx_order_order_type');
            $table->dropIndex('idx_order_order_date');
            $table->dropIndex('idx_order_created_at');
            $table->dropIndex('idx_order_supplier_id');
            $table->dropIndex('idx_order_status_order_status_created');
            $table->dropIndex('idx_order_user_id_created_at');
        });

        // 2. Table `order_info` rollback
        Schema::table('order_info', function (Blueprint $table) {
            $table->dropIndex('idx_order_info_order_id');
        });

        // 3. Table `transactions` rollback
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('idx_transactions_order_id');
        });

        // 4. Table `order_detail` rollback
        Schema::table('order_detail', function (Blueprint $table) {
            $table->dropIndex('idx_order_detail_order_id');
            $table->dropIndex('idx_order_detail_product_id');
        });
    }
};
