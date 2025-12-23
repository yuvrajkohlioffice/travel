<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lead_statuses', function (Blueprint $table) {
            $table->integer('order_by')->default(0)->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('lead_statuses', function (Blueprint $table) {
            $table->dropColumn('order_by');
        });
    }
};
