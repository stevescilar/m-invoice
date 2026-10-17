<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'phone',
        'email',
        'address',
        'is_flagged',
        'flag_reason'
    ];

    protected $casts = [
        'is_flagged' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function totalBilled(): float
    {
        return (float) $this->invoices()->sum('grand_total');
    }

    public function totalPaid(): float
    {
        $paidInvoicesTotal = (float) $this->invoices()->where('status', 'paid')->sum('grand_total');
        $partialPaidTotal = (float) $this->invoices()->where('status', '!=', 'paid')->sum('amount_paid');
        return $paidInvoicesTotal + $partialPaidTotal;
    }

    public function outstandingBalance(): float
    {
        return max(0, $this->totalBilled() - $this->totalPaid());
    }
}
