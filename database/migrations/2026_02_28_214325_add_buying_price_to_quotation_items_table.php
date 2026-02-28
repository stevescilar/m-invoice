<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->decimal('buying_price', 10, 2)->default(0)->after('unit_price');
            $table->decimal('profit', 10, 2)->default(0)->after('buying_price');
            $table->decimal('margin_percentage', 5, 2)->default(0)->after('profit');
        });
    }

    public function down(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->dropColumn(['buying_price', 'profit', 'margin_percentage']);
        });
    }
};