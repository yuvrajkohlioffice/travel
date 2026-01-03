<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();

            // Basic Info
            $table->string('name'); // Bank Transfer, Cash, UPI
            $table->enum('type', ['bank', 'cash', 'online', 'wallet'])->index();

            // Status
            $table->boolean('is_active')->default(true)->index();

            // Tax Information
            $table->boolean('is_tax_applicable')->default(false);
            $table->decimal('tax_percentage', 5, 2)->nullable(); // e.g. 18.00
            $table->string('tax_name')->nullable(); // GST, VAT
            $table->string('tax_number')->nullable(); // GSTIN

            // Bank Details (Optional)
            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code')->nullable();

            // Extra
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
