<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'client_id', 'invoice_number', 'issue_date', 'due_date',
        'status', 'etr_enabled', 'vat_amount', 'material_cost', 'labour_cost',
        'grand_total', 'total_cost', 'total_profit', 'overall_margin',
        'profit_from_quotation', 'notes', 'is_recurring', 'recurrence_interval',
        'next_recurrence_date', 'mpesa_code', 'paid_at', 'created_by'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'next_recurrence_date' => 'date',
        'paid_at' => 'datetime',
        'etr_enabled' => 'boolean',
        'is_recurring' => 'boolean',
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
        return $this->hasMany(InvoiceItem::class);
    }

    public function reminders()
    {
        return $this->hasMany(InvoiceReminder::class);
    }

    public function downloads()
    {
        return $this->hasMany(InvoiceDownload::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isOverdue(): bool
    {
        return $this->status !== 'paid' && $this->due_date?->isPast();
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['sent', 'overdue', 'stalled']);
    }
}