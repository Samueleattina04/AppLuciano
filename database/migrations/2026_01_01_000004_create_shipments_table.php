<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_code')->unique();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('container_number')->nullable();
            $table->string('seal_number')->nullable();
            $table->decimal('quantity_shipped', 14, 3)->default(0);
            $table->string('vessel_name')->nullable();
            $table->string('voyage_number')->nullable();
            $table->string('carrier')->nullable();
            $table->string('forwarder')->nullable();
            $table->string('bl_number')->nullable();
            $table->string('port_of_loading')->nullable();
            $table->string('port_of_discharge')->nullable();
            $table->date('etd')->nullable();
            $table->date('eta')->nullable();
            $table->date('actual_arrival_date')->nullable();
            $table->date('warehouse_arrival_date')->nullable();
            $table->string('status')->default('in_production');
            // in_production, ready_to_ship, at_port, in_transit, arrived_pod, customs_clearance, delivered_warehouse, closed
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
