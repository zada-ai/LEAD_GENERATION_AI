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
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('gmail_message_id')->unique()->nullable();
            $table->string('gmail_thread_id')->nullable();
            $table->string('message_id')->nullable();
            $table->string('in_reply_to')->nullable();
            $table->text('references')->nullable();
            $table->string('from_email');
            $table->string('from_name')->nullable();
            $table->string('to_email');
            $table->string('subject')->nullable();
            $table->longText('body_text')->nullable();
            $table->longText('body_html')->nullable();
            $table->text('snippet')->nullable();
            $table->enum('direction', ['incoming', 'outgoing'])->default('incoming');
            $table->dateTime('received_at')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            
            $table->index('email_account_id');
            $table->index('lead_id');
            $table->index('gmail_message_id');
            $table->index('gmail_thread_id');
            $table->index('from_email');
            $table->index('received_at');
            $table->index('is_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};
