<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop old tables if they exist
        Schema::dropIfExists('package_hotel');
        Schema::dropIfExists('package_car');

        // Create new combined table
        Schema::create('package_items', function (Blueprint $table) {
            $table->id();

            // Explicit foreign keys
            $table->unsignedBigInteger('package_id');
            $table->unsignedBigInteger('hotel_id')->nullable();
            $table->unsignedBigInteger('car_id')->nullable();

            $table->decimal('custom_price', 10, 2)->nullable();
            $table->boolean('already_price')->default(false); // true or false
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade');
            $table->foreign('car_id')->references('id')->on('cars')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_items');
    }
};
