<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('package_category', function (Blueprint $table) {
            // 1. Add company_id as an unsigned big integer
            // 2. constrained() automatically links it to the 'id' on the 'companies' table
            // 3. onDelete('cascade') ensures if a company is deleted, its categories are too
            $table->foreignId('company_id')
                  ->after('id') 
                  ->nullable()
                  ->constrained('companies')
                  ->onDelete('cascade');

            // 4. Add the is_global boolean column (defaulting to false/0)
            $table->boolean('is_global')->default(false)->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('package_category', function (Blueprint $table) {
            // Drop the foreign key first, then the columns
            $table->dropForeign(['company_id']);
            $table->dropColumn(['company_id', 'is_global']);
        });
    }
};