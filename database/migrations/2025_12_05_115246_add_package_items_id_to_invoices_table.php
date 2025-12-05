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
        Schema::table('invoices', function (Blueprint $table) {
            // Add nullable column for package_items_id
            $table->unsignedBigInteger('package_items_id')->nullable()->after('package_id');

            // Optional: Add foreign key if you want to enforce relationship
            $table->foreign('package_items_id')
                ->references('id')
                ->on('package_items')
                ->onDelete('set null'); // Set to null if package_item is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['package_items_id']);

            // Drop the column
            $table->dropColumn('package_items_id');
        });
    }
};
