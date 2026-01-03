<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            // Add the image_proof_required column
            $table->boolean('image_proof_required')->default(false)->after('description');

            // Add company_id column as nullable
            $table->unsignedBigInteger('company_id')->nullable()->after('image_proof_required');

            // Add foreign key (nullable)
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
            $table->dropColumn('image_proof_required');
        });
    }
};
