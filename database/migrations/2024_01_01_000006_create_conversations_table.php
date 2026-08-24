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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('email_account_id')->constrained()->cascadeOnDelete();
            $table->string('gmail_thread_id');
            $table->string('subject')->nullable();
            $table->enum('status', ['open', 'waiting', 'replied', 'closed'])->default('open');
            $table->dateTime('last_message_at')->nullable();
            $table->timestamps();
            
            $table->unique(['lead_id', 'gmail_thread_id']);
            $table->index('email_account_id');
            $table->index('gmail_thread_id');
            $table->index('last_message_at');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
