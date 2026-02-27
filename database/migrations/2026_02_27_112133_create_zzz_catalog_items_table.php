<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('catalog_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('default_unit_price', 10, 2)->default(0);
            $table->string('unit_of_measure')->default('piece');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('catalog_items'); }
};
