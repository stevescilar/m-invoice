<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->enum('status', ['draft','sent','paid','overdue','stalled'])->default('draft');
            $table->boolean('etr_enabled')->default(false);
            $table->decimal('vat_amount', 10, 2)->default(0);
            $table->decimal('material_cost', 10, 2)->default(0);
            $table->decimal('labour_cost', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurrence_interval', ['weekly','monthly'])->nullable();
            $table->date('next_recurrence_date')->nullable();
            $table->string('mpesa_code')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('invoices'); }
};
