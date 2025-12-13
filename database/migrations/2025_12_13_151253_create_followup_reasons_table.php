<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('followup_reasons', function (Blueprint $table) {
            $table->id();

            // Nullable company_id for global reasons
            $table->unsignedBigInteger('company_id')->nullable();

            $table->string('name');
            $table->text('remark')->nullable();
            $table->boolean('date')->default(false);
            $table->boolean('time')->default(false);
            $table->text('email_template')->nullable();
            $table->text('whatsapp_template')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_global')->default(false);

            $table->timestamps();

            // Foreign key constraint
            $table->foreign('company_id')
                  ->references('id')
                  ->on('companies')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('followup_reasons');
    }
};
