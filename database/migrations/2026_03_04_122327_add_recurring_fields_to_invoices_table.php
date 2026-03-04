<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            
            $table->enum('recurring_frequency', ['weekly', 'monthly', 'quarterly', 'yearly'])->nullable()->after('is_recurring');
            $table->date('recurring_next_date')->nullable()->after('recurring_frequency');
            $table->date('recurring_ends_at')->nullable()->after('recurring_next_date');
            $table->boolean('recurring_active')->default(true)->after('recurring_ends_at');
            $table->unsignedBigInteger('recurring_parent_id')->nullable()->after('recurring_active');
            $table->foreign('recurring_parent_id')->references('id')->on('invoices')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['recurring_parent_id']);
            $table->dropColumn(['is_recurring', 'recurring_frequency', 'recurring_next_date', 'recurring_ends_at', 'recurring_active', 'recurring_parent_id']);
        });
    }
};