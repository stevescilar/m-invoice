<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'company_id',
        'receipt_number',
        'amount',
        'payment_method',
        'reference',
        'paid_at',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function methodLabel(): string
    {
        return match ($this->payment_method) {
            'mpesa'  => 'M-Pesa',
            'bank'   => 'Bank Transfer',
            'cash'   => 'Cash',
            'cheque' => 'Cheque',
            default  => ucfirst($this->payment_method ?? 'Payment'),
        };
    }
}

