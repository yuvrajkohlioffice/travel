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
    Schema::create('hotels', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->enum('type', ['3-star', '4-star', '5-star', 'camp', 'guest-house']);
        $table->boolean('meal_included')->default(false);
        $table->string('meal_type')->nullable(); // breakfast/lunch/dinner/all
        $table->decimal('price', 10, 2)->nullable();
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('hotels');
}

};
