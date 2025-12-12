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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('company_name'); // Company Logo
            $table->text('scanner_details')->nullable()->after('logo'); // QR or scanner details
            $table->text('bank_details')->nullable()->after('scanner_details'); // Bank details JSON or text
            $table->string('whatsapp_api_key')->nullable()->after('bank_details'); // WhatsApp API key
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'logo',
                'scanner_details',
                'bank_details',
                'whatsapp_api_key',
            ]);
        });
    }
};
