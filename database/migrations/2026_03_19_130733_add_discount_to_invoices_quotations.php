<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('discount_amount', 10, 2)->default(0)->after('labour_cost');
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('discount_amount');
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->decimal('discount_amount', 10, 2)->default(0)->after('labour_cost');
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('discount_amount');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['discount_amount', 'discount_percentage']);
        });
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['discount_amount', 'discount_percentage']);
        });
    }
};