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
        Schema::create('containers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->string('container_number', 50)->unique();
            $table->enum('status', ['in_production', 'at_port', 'in_transit', 'customs_cleared', 'at_warehouse'])
                  ->default('in_production');
            $table->string('vessel_name', 100)->nullable();
            $table->string('voyage_number', 50)->nullable();
            $table->date('etd')->nullable();
            $table->date('eta')->nullable();
            $table->string('port_of_loading', 100)->nullable();
            $table->string('port_of_discharge', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('containers');
    }
};
