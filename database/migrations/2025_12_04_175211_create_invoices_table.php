<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // Invoice number example: TRAV-2023-0876
            $table->string('invoice_no')->unique();

            // Foreign references
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->unsignedBigInteger('package_id')->nullable();

            // Dates
            $table->date('issued_date')->nullable();
            $table->date('travel_start_date')->nullable();

            // Primary Traveler Information
            $table->string('primary_full_name');
            $table->string('primary_email')->nullable();
            $table->string('primary_phone')->nullable();
            $table->text('primary_address')->nullable();

            // Additional Travelers (JSON)
            $table->json('additional_travelers')->nullable();

            // Traveler Counts
            $table->integer('total_travelers')->default(1);
            $table->integer('adult_count')->default(1);
            $table->integer('child_count')->default(0);

            // Package Details
            $table->string('package_name')->nullable();
            $table->string('package_type')->nullable();
            $table->decimal('price_per_person', 10, 2)->default(0);

            // Pricing
            $table->decimal('subtotal_price', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('final_price', 10, 2)->default(0);

            // Extra Notes
            $table->text('additional_details')->nullable();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('lead_id')->references('id')->on('leads')->nullOnDelete();
            $table->foreign('package_id')->references('id')->on('packages')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
