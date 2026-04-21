<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_links', function (Blueprint $table) {
            $table->timestamp('last_parsed_at')->nullable()->after('settings');
            $table->text('last_parse_error')->nullable()->after('last_parsed_at');
        });
    }

    public function down(): void
    {
        Schema::table('product_links', function (Blueprint $table) {
            $table->dropColumn(['last_parsed_at', 'last_parse_error']);
        });
    }
};
