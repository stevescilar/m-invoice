<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'expired' to the quotations status ENUM
        DB::statement("ALTER TABLE quotations MODIFY COLUMN status ENUM('draft','sent','approved','rejected','converted','expired') NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        // First move any 'expired' rows back to 'sent' to avoid data loss on rollback
        DB::statement("UPDATE quotations SET status = 'sent' WHERE status = 'expired'");
        DB::statement("ALTER TABLE quotations MODIFY COLUMN status ENUM('draft','sent','approved','rejected','converted') NOT NULL DEFAULT 'draft'");
    }
};