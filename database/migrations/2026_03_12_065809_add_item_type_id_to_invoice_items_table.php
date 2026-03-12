<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->foreignId('item_type_id')->nullable()->after('is_labour')
                  ->constrained('item_types')->nullOnDelete();
        });

        Schema::table('quotation_items', function (Blueprint $table) {
            $table->foreignId('item_type_id')->nullable()->after('is_labour')
                  ->constrained('item_types')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropForeign(['item_type_id']);
            $table->dropColumn('item_type_id');
        });
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->dropForeign(['item_type_id']);
            $table->dropColumn('item_type_id');
        });
    }
};