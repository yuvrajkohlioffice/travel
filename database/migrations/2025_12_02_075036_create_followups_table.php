<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('followups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lead_id')->constrained()->onDelete('cascade'); // lead table relation

            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // who created followup

            // Followup main fields
            $table->string('reason')->nullable(); // Not Pickup, Interested, Call Back Later...
            $table->text('remark')->nullable();
            $table->date('next_followup_date')->nullable();
            $table->time('next_followup_time')->nullable();
            $table->dateTime('last_followup_date')->nullable();

            // System fields
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('followups');
    }
};
