<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubscriptionTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'plan', 'amount', 'mpesa_code', 'status'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}