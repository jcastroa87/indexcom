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
        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('index_id')->constrained('indices')->onDelete('cascade');
            $table->date('date');
            $table->decimal('value', 20, 6);
            $table->boolean('is_manual')->default(false);
            $table->timestamps();

            // Ensure we don't have duplicate dates for the same index
            $table->unique(['index_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rates');
    }
};
