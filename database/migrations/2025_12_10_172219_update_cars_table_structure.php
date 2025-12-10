<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('cars', function (Blueprint $table) {

        // Remove old columns if they exist
        if (Schema::hasColumn('cars', 'name')) {
            $table->dropColumn('name');
        }

        if (Schema::hasColumn('cars', 'type')) {
            $table->dropColumn('type');
        }

        if (Schema::hasColumn('cars', 'price')) {
            $table->dropColumn('price'); // JSON field
        }

        // Add the new clean structure
        if (!Schema::hasColumn('cars', 'car_type')) {
            $table->string('car_type')->after('id');
        }

        if (!Schema::hasColumn('cars', 'capacity')) {
            $table->integer('capacity')->after('car_type');
        }

        if (!Schema::hasColumn('cars', 'price_per_km')) {
            $table->decimal('price_per_km', 10, 2)->nullable()->after('capacity');
        }

        if (!Schema::hasColumn('cars', 'price_per_day')) {
            $table->decimal('price_per_day', 10, 2)->nullable()->after('price_per_km');
        }

    });
}

public function down()
{
    Schema::table('cars', function (Blueprint $table) {

        // Revert back the old structure
        $table->string('name')->nullable();
        $table->string('type')->nullable();
        $table->json('price')->nullable();

        $table->dropColumn(['car_type', 'capacity', 'price_per_km', 'price_per_day']);
    });
}


    
};
