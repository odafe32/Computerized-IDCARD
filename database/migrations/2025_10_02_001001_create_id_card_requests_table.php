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
            $table->string('request_number')->unique(); // Auto-generated request number
            $table->enum('reason', ['new', 'replacement', 'lost', 'damaged', 'name_change'])->default('new');
            $table->text('additional_info')->nullable();

            // Student photo for ID card
            $table->string('photo_path')->nullable();

            // Request status
            $table->enum('status', ['pending', 'approved', 'rejected', 'printed', 'ready', 'collected'])->default('pending');

            // Admin review
            $table->foreignUuid('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('admin_feedback')->nullable();

            // ID Card generation
            $table->string('card_number')->nullable()->unique(); // Generated card number
            $table->string('qr_code_path')->nullable(); // Path to QR code image
            $table->string('generated_card_path')->nullable(); // Path to generated ID card PDF
            $table->timestamp('printed_at')->nullable();
            $table->timestamp('collected_at')->nullable();

            // Tracking
            $table->string('collected_by')->nullable(); // Who collected the card
            $table->text('collection_notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index('status');
            $table->index('request_number');
            $table->index('card_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('id_card_requests');
    }
};
