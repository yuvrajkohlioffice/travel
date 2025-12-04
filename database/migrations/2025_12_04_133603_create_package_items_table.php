<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('package_items');

        Schema::create('package_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->foreignId('car_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('person_count')->default(1);
            $table->string('vehicle_name')->nullable();
            $table->integer('room_count')->default(1);

            // Room price options
            $table->decimal('standard_price', 10, 2)->nullable();
            $table->decimal('deluxe_price', 10, 2)->nullable();
            $table->decimal('luxury_price', 10, 2)->nullable();
            $table->decimal('premium_price', 10, 2)->nullable();


            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_items');
    }
};
