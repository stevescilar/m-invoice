<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('total_cost', 10, 2)->default(0)->after('grand_total');
            $table->decimal('total_profit', 10, 2)->default(0)->after('total_cost');
            $table->decimal('overall_margin', 5, 2)->default(0)->after('total_profit');
            $table->boolean('profit_from_quotation')->default(false)->after('overall_margin');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['total_cost', 'total_profit', 'overall_margin', 'profit_from_quotation']);
        });
    }
};
