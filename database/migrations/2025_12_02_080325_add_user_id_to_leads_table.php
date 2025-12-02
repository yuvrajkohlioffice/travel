<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            // 1. Add user_id nullable first
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
        });

        // 2. Fill existing rows with default user ID = 1 (or any valid ID)
        DB::table('leads')->update(['user_id' => 1]);

        // 3. Now add the foreign key
        Schema::table('leads', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
