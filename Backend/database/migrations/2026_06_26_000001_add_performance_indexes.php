<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function addIndexIfNotExists(string $table, string $column, string $indexName): void
    {
        $exists = DB::selectOne(
            "SELECT 1 FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND INDEX_NAME = ?",
            [$table, $indexName]
        );

        if (!$exists) {
            Schema::table($table, function ($table) use ($column, $indexName) {
                $table->index($column, $indexName);
            });
        }
    }

    public function up(): void
    {
        $this->addIndexIfNotExists('categories', 'is_active', 'categories_is_active_index');
        $this->addIndexIfNotExists('categories', 'name', 'categories_name_index');

        $this->addIndexIfNotExists('products', 'is_active', 'products_is_active_index');
        $this->addIndexIfNotExists('products', 'name', 'products_name_index');

        $this->addIndexIfNotExists('orders', 'status', 'orders_status_index');
        $this->addIndexIfNotExists('orders', 'created_at', 'orders_created_at_index');
    }

    public function down(): void
    {
        Schema::table('categories', function ($table) {
            $table->dropIndex(['parent_id']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['name']);
        });

        Schema::table('products', function ($table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['name']);
        });

        Schema::table('orders', function ($table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });
    }
};
