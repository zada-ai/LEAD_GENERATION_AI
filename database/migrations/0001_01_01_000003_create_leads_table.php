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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users');
            
            // Business Information
            $table->string('business_name');
            $table->string('category')->nullable();
            $table->string('website')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('linkedin_url')->nullable();
            
            // Contact Information
            $table->string('contact_name');
            $table->string('email');
            $table->string('phone')->nullable();
            
            // Location
            $table->string('country');
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('area')->nullable();
            
            // Sales Information
            $table->string('service_interest')->default('Other');
            $table->string('source')->default('Manual');
            $table->string('status')->default('new'); // new, contacted, interested, follow_up, converted, not_interested, do_not_contact
            $table->string('priority')->default('medium'); // low, medium, high
            $table->longText('notes')->nullable();
            
            // Follow-up Information
            $table->timestamp('last_contacted_at')->nullable();
            $table->timestamp('next_follow_up_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('created_by');
            $table->index('email');
            $table->index('status');
            $table->index('source');
            $table->index('category');
            $table->index('city');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
