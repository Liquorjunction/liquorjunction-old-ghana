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
        // 1. Table `order_tracking`: Tracking queries on order_id
        Schema::table('order_tracking', function (Blueprint $table) {
            $table->index('order_id', 'idx_order_tracking_order_id');
        });

        // 2. Table `transaction_token`: Payment callback queries
        Schema::table('transaction_token', function (Blueprint $table) {
            $table->index('transaction_token', 'idx_transaction_token_token');
            $table->index('order_id', 'idx_transaction_token_order_id');
        });

        // 3. Table `loyalty_points`: Loyalty calculations by customer
        Schema::table('loyalty_points', function (Blueprint $table) {
            $table->index('user_id', 'idx_loyalty_points_user_id');
        });

        // 4. Table `cart`: Active user cart lookups
        Schema::table('cart', function (Blueprint $table) {
            $table->index('user_id', 'idx_cart_user_id');
            $table->index('product_id', 'idx_cart_product_id');
        });

        // 5. Table `main_users`: Login, registration, and user search
        Schema::table('main_users', function (Blueprint $table) {
            $table->index('email', 'idx_main_users_email');
            $table->index('phone', 'idx_main_users_phone');
            $table->index('user_type', 'idx_main_users_user_type');
        });

        // 6. Table `user_address`: Customer addresses during checkout
        Schema::table('user_address', function (Blueprint $table) {
            $table->index('user_id', 'idx_user_address_user_id');
        });

        // 7. Table `admin_notifications`: Dashboard notification polling
        Schema::table('admin_notifications', function (Blueprint $table) {
            $table->index('order_id', 'idx_admin_notifications_order_id');
            $table->index('is_read', 'idx_admin_notifications_is_read');
        });

        // 8. Table `product`: Product listing, brand and category filtering
        Schema::table('product', function (Blueprint $table) {
            $table->index('category_id', 'idx_product_category_id');
            $table->index('subcategory_id', 'idx_product_subcategory_id');
            $table->index('brand_id', 'idx_product_brand_id');
            $table->index('status', 'idx_product_status');
        });

        // 9. Table `product_variants`: Variant fetching for products
        Schema::table('product_variants', function (Blueprint $table) {
            $table->index('product_id', 'idx_product_variants_product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_tracking', function (Blueprint $table) {
            $table->dropIndex('idx_order_tracking_order_id');
        });

        Schema::table('transaction_token', function (Blueprint $table) {
            $table->dropIndex('idx_transaction_token_token');
            $table->dropIndex('idx_transaction_token_order_id');
        });

        Schema::table('loyalty_points', function (Blueprint $table) {
            $table->dropIndex('idx_loyalty_points_user_id');
        });

        Schema::table('cart', function (Blueprint $table) {
            $table->dropIndex('idx_cart_user_id');
            $table->dropIndex('idx_cart_product_id');
        });

        Schema::table('main_users', function (Blueprint $table) {
            $table->dropIndex('idx_main_users_email');
            $table->dropIndex('idx_main_users_phone');
            $table->dropIndex('idx_main_users_user_type');
        });

        Schema::table('user_address', function (Blueprint $table) {
            $table->dropIndex('idx_user_address_user_id');
        });

        Schema::table('admin_notifications', function (Blueprint $table) {
            $table->dropIndex('idx_admin_notifications_order_id');
            $table->dropIndex('idx_admin_notifications_is_read');
        });

        Schema::table('product', function (Blueprint $table) {
            $table->dropIndex('idx_product_category_id');
            $table->dropIndex('idx_product_subcategory_id');
            $table->dropIndex('idx_product_brand_id');
            $table->dropIndex('idx_product_status');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex('idx_product_variants_product_id');
        });
    }
};
