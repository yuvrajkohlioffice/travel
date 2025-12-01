<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('package_type_id')->constrained('package_types')->onDelete('cascade');
            $table->foreignId('package_category_id')->constrained('package_category')->onDelete('cascade');
            $table->foreignId('difficulty_type_id')->constrained('difficulty_types')->onDelete('cascade');

            // Package details
            $table->string('pickup_points');
            $table->string('package_name');
            $table->string('package_docs')->nullable();
            $table->string('package_banner')->nullable();
            $table->json('other_images')->nullable();
            $table->integer('package_days')->default(1);
            $table->integer('package_nights')->default(0);
            $table->decimal('package_price', 12, 2)->default(0.0);
            $table->decimal('altitude', 8, 2)->nullable();
            $table->integer('min_age')->nullable();
            $table->integer('max_age')->nullable();
            $table->string('best_time_to_visit')->nullable();
            $table->text('content')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
