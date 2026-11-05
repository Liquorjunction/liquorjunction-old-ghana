<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Check if an index exists on a table.
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        $indexes = DB::select("SHOW INDEXES FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Indexes on `order` table
        if (Schema::hasTable('order')) {
            Schema::table('order', function (Blueprint $table) {
                if (!$this->hasIndex('order', 'idx_order_order_id')) {
                    $table->index('order_id', 'idx_order_order_id');
                }
                if (!$this->hasIndex('order', 'idx_order_uniqid')) {
                    $table->index('uniqid', 'idx_order_uniqid');
                }
                if (!$this->hasIndex('order', 'idx_order_status')) {
                    $table->index('status', 'idx_order_status');
                }
                if (!$this->hasIndex('order', 'idx_order_order_status')) {
                    $table->index('order_status', 'idx_order_order_status');
                }
                if (!$this->hasIndex('order', 'idx_order_order_type')) {
                    $table->index('order_type', 'idx_order_order_type');
                }
                if (!$this->hasIndex('order', 'idx_order_order_date')) {
                    $table->index('order_date', 'idx_order_order_date');
                }
                if (!$this->hasIndex('order', 'idx_order_created_at')) {
                    $table->index('created_at', 'idx_order_created_at');
                }
                if (!$this->hasIndex('order', 'idx_order_supplier_id')) {
                    $table->index('supplier_id', 'idx_order_supplier_id');
                }
                if (!$this->hasIndex('order', 'idx_order_status_order_status_created')) {
                    $table->index(['status', 'order_status', 'created_at'], 'idx_order_status_order_status_created');
                }
                if (!$this->hasIndex('order', 'idx_order_user_id_created_at')) {
                    $table->index(['user_id', 'created_at'], 'idx_order_user_id_created_at');
                }
            });
        }

        // 2. Index on `order_info` table (critical for joins on order.id)
        if (Schema::hasTable('order_info')) {
            Schema::table('order_info', function (Blueprint $table) {
                if (!$this->hasIndex('order_info', 'idx_order_info_order_id')) {
                    $table->index('order_id', 'idx_order_info_order_id');
                }
            });
        }

        // 3. Index on `transactions` table (critical for joins on order.id)
        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (!$this->hasIndex('transactions', 'idx_transactions_order_id')) {
                    $table->index('order_id', 'idx_transactions_order_id');
                }
            });
        }

        // 4. Indexes on `order_detail` table (critical for details & analytics)
        if (Schema::hasTable('order_detail')) {
            Schema::table('order_detail', function (Blueprint $table) {
                if (!$this->hasIndex('order_detail', 'idx_order_detail_order_id')) {
                    $table->index('order_id', 'idx_order_detail_order_id');
                }
                if (!$this->hasIndex('order_detail', 'idx_order_detail_product_id')) {
                    $table->index('product_id', 'idx_order_detail_product_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('order')) {
            Schema::table('order', function (Blueprint $table) {
                $indexes = [
                    'idx_order_order_id',
                    'idx_order_uniqid',
                    'idx_order_status',
                    'idx_order_order_status',
                    'idx_order_order_type',
                    'idx_order_order_date',
                    'idx_order_created_at',
                    'idx_order_supplier_id',
                    'idx_order_status_order_status_created',
                    'idx_order_user_id_created_at',
                ];
                foreach ($indexes as $index) {
                    if ($this->hasIndex('order', $index)) {
                        $table->dropIndex($index);
                    }
                }
            });
        }

        if (Schema::hasTable('order_info')) {
            Schema::table('order_info', function (Blueprint $table) {
                if ($this->hasIndex('order_info', 'idx_order_info_order_id')) {
                    $table->dropIndex('idx_order_info_order_id');
                }
            });
        }

        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                if ($this->hasIndex('transactions', 'idx_transactions_order_id')) {
                    $table->dropIndex('idx_transactions_order_id');
                }
            });
        }

        if (Schema::hasTable('order_detail')) {
            Schema::table('order_detail', function (Blueprint $table) {
                if ($this->hasIndex('order_detail', 'idx_order_detail_order_id')) {
                    $table->dropIndex('idx_order_detail_order_id');
                }
                if ($this->hasIndex('order_detail', 'idx_order_detail_product_id')) {
                    $table->dropIndex('idx_order_detail_product_id');
                }
            });
        }
    }
};
