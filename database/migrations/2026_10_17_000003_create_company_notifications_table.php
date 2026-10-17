<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Enhance existing notifications table with action_url, client_comment, meta, and read_at
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                if (!Schema::hasColumn('notifications', 'client_comment')) {
                    $table->text('client_comment')->nullable()->after('message');
                }
                if (!Schema::hasColumn('notifications', 'action_url')) {
                    $table->string('action_url')->nullable()->after('client_comment');
                }
                if (!Schema::hasColumn('notifications', 'meta')) {
                    $table->json('meta')->nullable()->after('action_url');
                }
                if (!Schema::hasColumn('notifications', 'read_at')) {
                    $table->timestamp('read_at')->nullable()->after('is_read');
                }
            });
        }

        // Store client response notes directly on quotations and invoices as well
        Schema::table('quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('quotations', 'client_response_note')) {
                $table->text('client_response_note')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('quotations', 'responded_at')) {
                $table->timestamp('responded_at')->nullable()->after('client_response_note');
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'client_payment_note')) {
                $table->text('client_payment_note')->nullable()->after('mpesa_code');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                $drop = [];
                foreach (['client_comment', 'action_url', 'meta', 'read_at'] as $col) {
                    if (Schema::hasColumn('notifications', $col)) {
                        $drop[] = $col;
                    }
                }
                if (!empty($drop)) {
                    $table->dropColumn($drop);
                }
            });
        }

        Schema::table('quotations', function (Blueprint $table) {
            $drop = [];
            if (Schema::hasColumn('quotations', 'client_response_note')) {
                $drop[] = 'client_response_note';
            }
            if (Schema::hasColumn('quotations', 'responded_at')) {
                $drop[] = 'responded_at';
            }
            if (!empty($drop)) {
                $table->dropColumn($drop);
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'client_payment_note')) {
                $table->dropColumn('client_payment_note');
            }
        });
    }
};

