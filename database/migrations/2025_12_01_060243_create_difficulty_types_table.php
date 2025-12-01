<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('difficulty_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('level')->default(1); // Example: 1 = Easy, 2 = Medium, 3 = Hard
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('difficulty_types');
    }
};
