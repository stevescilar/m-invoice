<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('invoice_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('downloaded_by')->constrained('users')->cascadeOnDelete();
            $table->boolean('charged')->default(false);
            $table->decimal('amount_charged', 10, 2)->default(0);
            $table->string('mpesa_code')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('invoice_downloads'); }
};
