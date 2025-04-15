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
        Schema::create('api_request_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->string('endpoint');
            $table->string('method');
            $table->string('ip_address')->nullable();
            $table->integer('response_code');
            $table->float('response_time')->nullable(); // in seconds
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Add indexes for quick filtering
            $table->index('user_id');
            $table->index('subscription_id');
            $table->index('endpoint');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_request_logs');
    }
};
