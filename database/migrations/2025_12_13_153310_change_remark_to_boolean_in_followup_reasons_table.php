<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First, update existing text values to 0 (false)
        DB::table('followup_reasons')->update(['remark' => 0]);

        // Then, change column type
        Schema::table('followup_reasons', function (Blueprint $table) {
            $table->boolean('remark')->default(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('followup_reasons', function (Blueprint $table) {
            $table->text('remark')->nullable()->change();
        });
    }
};
