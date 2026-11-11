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
        $tableColumns = [
            'area' => ['title_fr'],
            'banners' => ['language_id', 'title_fr', 'description_fr'],
            'blog' => ['title_fr', 'short_description_fr', 'long_description_fr'],
            'brand' => ['title_fr'],
            'categories' => ['language_id', 'title_fr', 'description_fr'],
            'cms' => ['language_id', 'page_name_fr', 'page_content_fr', 'mobile_page_content_fr'],
            'emailtemplates' => ['language_id'],
            'faqs' => ['language_id', 'question_name_fr', 'answer_fr'],
            'inquiry_reason' => ['title_fr'],
            'labels' => ['language_id', 'label_value_fr'],
            'order_status' => ['name_fr'],
            'product' => ['product_name_fr', 'short_description_fr', 'page_content_fr'],
            'regions' => ['title_fr'],
            'settings' => ['language_id', 'address_fr'],
            'store_details' => ['store_name_fr'],
            'sub_categories' => ['title_fr', 'description_fr'],
            'uofs' => ['title_fr'],
        ];

        foreach ($tableColumns as $tableName => $columns) {
            if (Schema::hasTable($tableName)) {
                $existingColumns = [];
                foreach ($columns as $column) {
                    if (Schema::hasColumn($tableName, $column)) {
                        $existingColumns[] = $column;
                    }
                }
                if (!empty($existingColumns)) {
                    Schema::table($tableName, function (Blueprint $table) use ($existingColumns) {
                        $table->dropColumn($existingColumns);
                    });
                }
            }
        }

        if (Schema::hasTable('languages')) {
            Schema::dropIfExists('languages');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Re-create columns as nullable if rollback is executed
    }
};
