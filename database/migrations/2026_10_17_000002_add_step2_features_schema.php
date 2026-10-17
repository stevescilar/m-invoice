<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add amount_paid and public_token to invoices
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'amount_paid')) {
                $table->decimal('amount_paid', 10, 2)->default(0)->after('grand_total');
            }
            if (!Schema::hasColumn('invoices', 'public_token')) {
                $table->string('public_token', 64)->nullable()->unique()->after('invoice_number');
            }
        });

        // 2. Add public_token and approved_at to quotations
        Schema::table('quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('quotations', 'public_token')) {
                $table->string('public_token', 64)->nullable()->unique()->after('quotation_number');
            }
            if (!Schema::hasColumn('quotations', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('status');
            }
        });

        // 3. Create invoice_payments table for partial payments, deposits & receipts
        if (!Schema::hasTable('invoice_payments')) {
            Schema::create('invoice_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->string('receipt_number', 50);
                $table->decimal('amount', 10, 2);
                $table->string('payment_method', 30)->default('mpesa'); // mpesa, bank, cash, cheque, other
                $table->string('reference', 100)->nullable();
                $table->dateTime('paid_at');
                $table->text('notes')->nullable();
                $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // 4. Backfill amount_paid for already-paid invoices
        DB::table('invoices')
            ->where('status', 'paid')
            ->where('amount_paid', 0)
            ->update(['amount_paid' => DB::raw('grand_total')]);

        // 5. Backfill public_token for existing invoices and quotations
        $invoices = DB::table('invoices')->whereNull('public_token')->select('id')->get();
        foreach ($invoices as $inv) {
            DB::table('invoices')->where('id', $inv->id)->update([
                'public_token' => Str::random(32),
            ]);
        }

        $quotations = DB::table('quotations')->whereNull('public_token')->select('id')->get();
        foreach ($quotations as $quo) {
            DB::table('quotations')->where('id', $quo->id)->update([
                'public_token' => Str::random(32),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_payments');

        Schema::table('invoices', function (Blueprint $table) {
            $drop = [];
            if (Schema::hasColumn('invoices', 'amount_paid')) {
                $drop[] = 'amount_paid';
            }
            if (Schema::hasColumn('invoices', 'public_token')) {
                $drop[] = 'public_token';
            }
            if (!empty($drop)) {
                $table->dropColumn($drop);
            }
        });

        Schema::table('quotations', function (Blueprint $table) {
            $drop = [];
            if (Schema::hasColumn('quotations', 'public_token')) {
                $drop[] = 'public_token';
            }
            if (Schema::hasColumn('quotations', 'approved_at')) {
                $drop[] = 'approved_at';
            }
            if (!empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }
};

