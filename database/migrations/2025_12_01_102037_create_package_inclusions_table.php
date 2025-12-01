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
    Schema::create('package_inclusions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('package_id')->constrained()->onDelete('cascade');
        $table->string('title');           // e.g. Guide, Trek Stick, Oxygen Cylinder
        $table->boolean('included')->default(true);
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('package_inclusions');
}

};
