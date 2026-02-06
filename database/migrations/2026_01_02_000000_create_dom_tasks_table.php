<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dom_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_link_id')->nullable()->constrained('product_links')->nullOnDelete();
            $table->string('url');
            $table->string('marketplace_code', 50);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->string('worker_id', 100)->nullable();
            $table->longText('dom_content')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('worker_id');
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dom_tasks');
    }
};
