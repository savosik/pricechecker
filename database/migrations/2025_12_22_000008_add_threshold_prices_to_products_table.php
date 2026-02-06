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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('ozon_threshold_price', 10, 2)->nullable()->after('tracking_urls');
            $table->decimal('wildberries_threshold_price', 10, 2)->nullable()->after('ozon_threshold_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['ozon_threshold_price', 'wildberries_threshold_price']);
        });
    }
};
