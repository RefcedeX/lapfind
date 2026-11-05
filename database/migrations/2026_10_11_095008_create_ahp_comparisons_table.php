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
        Schema::create('ahp_comparisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('criteria_a_id')->constrained('criteria')->cascadeOnDelete();
            $table->foreignId('criteria_b_id')->constrained('criteria')->cascadeOnDelete();
            $table->decimal('value', 8, 4);
            $table->timestamps();

            $table->index(['user_id', 'criteria_a_id', 'criteria_b_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ahp_comparisons');
    }
};
