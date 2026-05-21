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
        Schema::table('contracts', function (Blueprint $table) {
            $table->decimal('kg_per_unit', 12, 4)->default(1)->after('unit_of_measure');
            $table->decimal('exchange_rate_to_eur', 10, 6)->default(1)->after('currency');
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn(['kg_per_unit', 'exchange_rate_to_eur']);
        });
    }
};
