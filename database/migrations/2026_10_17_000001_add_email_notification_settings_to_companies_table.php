<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('companies')) {
            Schema::table('companies', function (Blueprint $table) {
                if (!Schema::hasColumn('companies', 'cc_owner_on_emails')) {
                    $table->boolean('cc_owner_on_emails')->default(true)->after('primary_color');
                }
                if (!Schema::hasColumn('companies', 'notification_email')) {
                    $table->string('notification_email')->nullable()->after('cc_owner_on_emails');
                }
                if (!Schema::hasColumn('companies', 'auto_invoice_reminders')) {
                    $table->boolean('auto_invoice_reminders')->default(true)->after('notification_email');
                }
                if (!Schema::hasColumn('companies', 'auto_quotation_reminders')) {
                    $table->boolean('auto_quotation_reminders')->default(true)->after('auto_invoice_reminders');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('companies')) {
            Schema::table('companies', function (Blueprint $table) {
                $columns = array_filter(
                    ['cc_owner_on_emails', 'notification_email', 'auto_invoice_reminders', 'auto_quotation_reminders'],
                    fn ($col) => Schema::hasColumn('companies', $col)
                );
                if (!empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
