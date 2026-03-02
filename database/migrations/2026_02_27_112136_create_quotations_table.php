<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('quotation_number')->unique();
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['draft','sent','approved','rejected','converted'])->default('draft');
            $table->decimal('material_cost', 10, 2)->default(0);
            $table->decimal('labour_cost', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('converted_invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('quotations'); }
};
