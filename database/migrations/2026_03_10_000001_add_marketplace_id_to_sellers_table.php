<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->foreignId('marketplace_id')
                ->nullable()
                ->after('external_id')
                ->constrained('marketplaces')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->dropForeign(['marketplace_id']);
            $table->dropColumn('marketplace_id');
        });
    }
};
