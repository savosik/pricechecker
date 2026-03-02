<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_links', function (Blueprint $table) {
            $table->unique(
                ['product_id', 'marketplace_id', 'seller_id'],
                'product_links_product_marketplace_seller_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_links', function (Blueprint $table) {
            $table->dropUnique('product_links_product_marketplace_seller_unique');
        });
    }
};
