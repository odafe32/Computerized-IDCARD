<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('id_card_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');

            // Request details
            $table->string('request_number')->unique(); // Auto-generated: IDR-2024-001
            $table->string('photo')->nullable(); // Uploaded photo path
            $table->text('reason')->nullable(); // Reason for request (new/replacement/lost)

            // Status tracking
            $table->enum('status', ['pending', 'approved', 'rejected', 'printed', 'ready', 'collected'])
                  ->default('pending');
            $table->text('admin_feedback')->nullable(); // Admin comments/rejection reason
            $table->string('id_card_file')->nullable(); // Generated ID card PDF path

            // Admin tracking
            $table->foreignUuid('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('printed_at')->nullable();
            $table->timestamp('collected_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index('request_number');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('id_card_requests');
    }
};
