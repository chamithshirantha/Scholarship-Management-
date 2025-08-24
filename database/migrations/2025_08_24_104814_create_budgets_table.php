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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scholarship_id')->constrained();
            $table->foreignId('cost_category_id')->constrained();
            $table->decimal('allocated_amount', 10, 2);
            $table->decimal('utilized_amount', 10, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['scholarship_id', 'cost_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
