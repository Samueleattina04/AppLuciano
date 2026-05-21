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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('doc_ref_type')->nullable()->after('bank_reference');
            $table->string('doc_ref_number')->nullable()->after('doc_ref_type');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['doc_ref_type', 'doc_ref_number']);
        });
    }
};
