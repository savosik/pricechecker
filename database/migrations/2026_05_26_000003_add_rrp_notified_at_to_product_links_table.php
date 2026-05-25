<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_links', function (Blueprint $table) {
            $table->timestamp('rrp_notified_at')->nullable()->after('last_parse_error');
        });
    }

    public function down(): void
    {
        Schema::table('product_links', function (Blueprint $table) {
            $table->dropColumn('rrp_notified_at');
        });
    }
};
