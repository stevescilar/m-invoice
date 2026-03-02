<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionTransaction extends Model
{
    protected $fillable = [
        'company_id', 'plan', 'amount', 'mpesa_code',
        'status', 'checkout_request_id', 'reference'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}