<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('mpesa_paybill')->nullable();
            $table->string('mpesa_account')->nullable();
            $table->string('mpesa_till')->nullable();
            $table->string('mpesa_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_branch')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'mpesa_paybill',
                'mpesa_account',
                'mpesa_till',
                'mpesa_number',
                'bank_name',
                'bank_account',
                'bank_branch'
            ]);
        });
    }
};
