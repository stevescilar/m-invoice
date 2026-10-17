<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add buying_price to invoice_items if missing
        if (Schema::hasTable('invoice_items') && !Schema::hasColumn('invoice_items', 'buying_price')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->decimal('buying_price', 10, 2)->default(0)->after('unit_price');
            });
        }

        // 2. Fix subscription_transactions plan & status ENUMs for M-Pesa callbacks and per_invoice
        if (Schema::hasTable('subscription_transactions')) {
            DB::statement("ALTER TABLE `subscription_transactions` MODIFY COLUMN `plan` ENUM('per_invoice','monthly','yearly') NOT NULL");
            DB::statement("ALTER TABLE `subscription_transactions` MODIFY COLUMN `status` ENUM('completed','success','failed','pending') NOT NULL DEFAULT 'pending'");
        }

        // 3. Fix invoice_reminders status ENUM to allow 'skipped'
        if (Schema::hasTable('invoice_reminders')) {
            DB::statement("ALTER TABLE `invoice_reminders` MODIFY COLUMN `status` ENUM('pending','sent','failed','skipped') NOT NULL DEFAULT 'pending'");
        }

        // 4. Scope invoice_number and quotation_number uniqueness per company_id
        if (Schema::hasTable('invoices')) {
            try {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->dropUnique('invoices_invoice_number_unique');
                    $table->unique(['company_id', 'invoice_number'], 'invoices_company_invoice_number_unique');
                });
            } catch (\Throwable $e) {
                // Index already updated
            }
        }

        if (Schema::hasTable('quotations')) {
            try {
                Schema::table('quotations', function (Blueprint $table) {
                    $table->dropUnique('quotations_quotation_number_unique');
                    $table->unique(['company_id', 'quotation_number'], 'quotations_company_quotation_number_unique');
                });
            } catch (\Throwable $e) {
                // Index already updated
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('invoice_items') && Schema::hasColumn('invoice_items', 'buying_price')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->dropColumn('buying_price');
            });
        }
    }
};

