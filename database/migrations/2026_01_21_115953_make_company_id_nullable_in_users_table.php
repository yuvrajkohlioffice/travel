<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Make company_id nullable
            // assuming it is an unsignedBigInteger (standard for IDs)
            $table->unsignedBigInteger('company_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert back to NOT NULL
            // Note: This will fail if you have NULL values in the column when rolling back
            $table->unsignedBigInteger('company_id')->nullable(false)->change();
        });
    }
};