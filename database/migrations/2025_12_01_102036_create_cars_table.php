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
    Schema::create('cars', function (Blueprint $table) {
        $table->id();
        $table->string('car_type'); // SUV, Sedan, Traveller etc
        $table->integer('capacity');
        $table->decimal('price_per_km', 10, 2)->nullable();
        $table->decimal('price_per_day', 10, 2)->nullable();
        $table->timestamps();
    });
}


public function down()
{
    Schema::dropIfExists('cars');
}

};
