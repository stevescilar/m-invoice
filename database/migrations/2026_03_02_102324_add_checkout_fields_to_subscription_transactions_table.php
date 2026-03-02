<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_transactions', function (Blueprint $table) {
            $table->string('checkout_request_id')->nullable()->after('mpesa_code');
            $table->string('reference')->nullable()->after('checkout_request_id');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_transactions', function (Blueprint $table) {
            $table->dropColumn(['checkout_request_id', 'reference']);
        });
    }
};