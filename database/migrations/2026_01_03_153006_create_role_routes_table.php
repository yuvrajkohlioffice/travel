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
        Schema::create('role_routes', function (Blueprint $table) {
            $table->id(); // id column
            $table->unsignedBigInteger('role_id'); // role_id
            $table->string('route_name'); // route_name
            $table->timestamps(); // created_at and updated_at

            // Optional: Add foreign key if you have a roles table
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_routes');
    }
};
