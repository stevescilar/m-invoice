<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'client_id', 'quotation_number', 'issue_date', 'expiry_date',
        'status', 'approved_at', 'material_cost', 'labour_cost', 'grand_total', 'notes',
        'converted_invoice_id', 'created_by', 'total_cost', 'total_profit', 'overall_margin',
        'discount_amount', 'discount_percentage', 'public_token',
    ];

    protected $casts = [
        'issue_date'  => 'date',
        'expiry_date' => 'date',
        'approved_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Quotation $quotation) {
            if (empty($quotation->public_token)) {
                $quotation->public_token = \Illuminate\Support\Str::random(32);
            }
        });
    }

    public function ensurePublicToken(): string
    {
        if (empty($this->public_token)) {
            $this->public_token = \Illuminate\Support\Str::random(32);
            $this->saveQuietly();
        }
        return $this->public_token;
    }

    public function getPublicUrlAttribute(): string
    {
        return route('public.quotation.show', $this->ensurePublicToken());
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function convertedInvoice()
    {
        return $this->belongsTo(Invoice::class, 'converted_invoice_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
