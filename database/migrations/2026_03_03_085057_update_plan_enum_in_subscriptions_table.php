<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE subscriptions MODIFY COLUMN plan ENUM('trial','free','per_invoice','monthly','yearly') NOT NULL DEFAULT 'trial'");
        DB::statement("ALTER TABLE subscriptions MODIFY COLUMN status ENUM('trial','active','expired','cancelled') NOT NULL DEFAULT 'trial'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE subscriptions MODIFY COLUMN plan ENUM('free','monthly','yearly') NOT NULL DEFAULT 'free'");
        DB::statement("ALTER TABLE subscriptions MODIFY COLUMN status ENUM('active','expired','cancelled') NOT NULL DEFAULT 'active'");
    }
};