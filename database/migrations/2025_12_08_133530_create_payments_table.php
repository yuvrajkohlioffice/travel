<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            
            // Linking payment to invoice
            $table->unsignedBigInteger('invoice_id');
            
            // Optional: link to user who made the payment
            $table->unsignedBigInteger('user_id')->nullable();
            
            // Payment details
            $table->decimal('amount', 12, 2); // Total invoice amount
            $table->decimal('paid_amount', 12, 2)->default(0); // How much paid
            $table->decimal('remaining_amount', 12, 2)->default(0); // Remaining amount
            $table->enum('status', ['pending', 'partial', 'completed', 'failed', 'refunded'])->default('pending');
            $table->enum('payment_method', ['bank_transfer', 'card', 'cash', 'paypal', 'other'])->nullable();
            $table->string('transaction_id')->nullable();
            $table->text('notes')->nullable(); // log / remarks
            
            // Partial payment tracking
            $table->date('next_payment_date')->nullable(); // next installment due
            $table->date('reminder_date')->nullable(); // date to send reminder
            
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
