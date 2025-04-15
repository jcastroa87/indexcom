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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->string('status'); // active, canceled, expired
            $table->json('metadata')->nullable(); // Additional data
            $table->integer('api_requests_today')->default(0);
            $table->date('api_requests_reset_date')->nullable();
            $table->timestamps();

            // Create an index on the user_id
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
