<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->unique();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->default('single'); // framework, single
            $table->string('crop_season')->nullable();
            $table->decimal('quantity_contracted', 14, 3)->default(0);
            $table->string('unit_of_measure')->default('kg');
            $table->decimal('unit_price', 14, 4)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->string('incoterm', 20)->nullable();
            $table->string('port_of_loading')->nullable();
            $table->string('port_of_discharge')->nullable();
            $table->date('contract_date')->nullable();
            $table->date('shipment_window_start')->nullable();
            $table->date('shipment_window_end')->nullable();
            $table->text('payment_terms_description')->nullable();
            $table->decimal('total_value', 16, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('status')->default('draft'); // draft, confirmed, partially_shipped, completed, cancelled
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
