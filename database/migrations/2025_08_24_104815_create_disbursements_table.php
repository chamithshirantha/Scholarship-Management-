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
        Schema::create('disbursements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('award_id')->constrained();
            $table->foreignId('cost_category_id')->constrained();
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['scheduled', 'paid', 'cancelled'])->default('scheduled');
            $table->date('scheduled_date');
            $table->date('paid_date')->nullable();
            $table->text('payment_details')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disbursements');
    }
};
