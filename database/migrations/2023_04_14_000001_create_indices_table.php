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
        Schema::create('indices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('source_api_url')->nullable();
            $table->string('source_api_key')->nullable();
            $table->string('source_api_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_fetch_at')->nullable();
            $table->integer('fetch_frequency')->default(60); // default to 60 minutes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indices');
    }
};
