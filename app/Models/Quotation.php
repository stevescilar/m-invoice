<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
    'company_id', 'client_id', 'quotation_number', 'issue_date', 'expiry_date',
    'status', 'material_cost', 'labour_cost', 'grand_total', 'notes',
    'converted_invoice_id', 'created_by', 'total_cost', 'total_profit', 'overall_margin','discount_amount',
    'discount_percentage',
];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

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
