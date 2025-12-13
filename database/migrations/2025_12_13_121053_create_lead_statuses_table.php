<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->comment('Nullable for global status');
            $table->string('name')->unique();
            $table->string('color')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_global')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_statuses');
    }
};
