<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communication_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->string('sender')->nullable();
            $table->string('recipient')->nullable();
            $table->string('outlook_thread_link')->nullable();
            $table->string('outlook_message_id')->nullable();
            $table->string('outlook_conversation_id')->nullable();
            $table->string('category')->default('internal_note');
            // document_approval, cad_bank_request, payment_followup, supplier_request, forwarder_request, internal_note
            $table->string('priority')->default('normal');
            // low, normal, high, urgent
            $table->date('due_date')->nullable();
            $table->date('reminder_date')->nullable();
            $table->string('status')->default('to_review');
            // to_review, waiting_internal, waiting_supplier, ready_to_reply, replied, closed
            $table->text('notes')->nullable();
            $table->foreignId('contract_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shipment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communication_tasks');
    }
};
