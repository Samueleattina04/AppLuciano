<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->morphs('documentable');
            $table->string('document_type');
            // bill_of_lading, commercial_invoice, packing_list, certificate_of_origin,
            // phytosanitary_certificate, insurance_certificate, quality_certificate, other
            $table->string('name');
            $table->string('file_path')->nullable();
            $table->integer('version')->default(1);
            $table->string('status')->default('received'); // missing, received, under_review, approved, rejected
            $table->text('notes')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
