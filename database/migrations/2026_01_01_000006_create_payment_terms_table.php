<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->decimal('percentage', 5, 2)->nullable();
            $table->decimal('amount', 14, 2)->nullable();
            $table->string('currency', 10)->default('USD');
            $table->string('due_trigger')->default('contract_date');
            // contract_date, etd, eta, bl_date, arrival_date, custom
            $table->integer('due_days_offset')->default(0);
            $table->date('custom_due_date')->nullable();
            $table->integer('order')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_terms');
    }
};
