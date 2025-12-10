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
        Schema::create('message_templates', function (Blueprint $table) {
            $table->id();

            // Package (Required)
            $table->unsignedBigInteger('package_id');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');

            // WhatsApp fields
            $table->text('whatsapp_text')->nullable();
            $table->string('whatsapp_media')->nullable(); // file/image path

            // Email fields
            $table->string('email_subject')->nullable();
            $table->longText('email_body')->nullable();
            $table->string('email_media')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_templates');
    }
};
