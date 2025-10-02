<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');

            // Notification details
            $table->string('title');
            $table->text('message');
            $table->string('type')->default('info'); // success, info, warning, danger
            $table->string('icon')->nullable(); // mdi icon class

            // Related data
            $table->string('related_type')->nullable(); // Model class name
            $table->uuid('related_id')->nullable(); // Model ID
            $table->json('data')->nullable(); // Additional data

            // Status
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'created_at']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
