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
        'next_recurrence_date', 'mpesa_code', 'paid_at', 'created_by','recurring_frequency',
        'recurring_next_date', 'recurring_ends_at', 'recurring_active', 'recurring_parent_id',
        'discount_amount','discount_percentage', 'amount_paid', 'public_token',
    ];

    protected $casts = [
        'issue_date'          => 'date',
        'due_date'            => 'date',
        'recurring_next_date' => 'date',
        'recurring_ends_at'   => 'date',
        'paid_at'             => 'datetime',
        'etr_enabled'         => 'boolean',
        'is_recurring'        => 'boolean',
        'recurring_active'    => 'boolean',
        'amount_paid'         => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            if (empty($invoice->public_token)) {
                $invoice->public_token = \Illuminate\Support\Str::random(32);
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
        return route('public.invoice.show', $this->ensurePublicToken());
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
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(InvoicePayment::class)->orderByDesc('paid_at')->orderByDesc('id');
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

    public function totalPaidAmount(): float
    {
        if ($this->status === 'paid') {
            return (float) $this->grand_total;
        }
        return min((float) $this->grand_total, max(0, (float) ($this->amount_paid ?? 0)));
    }

    public function balanceDue(): float
    {
        return max(0, round((float) $this->grand_total - $this->totalPaidAmount(), 2));
    }

    public function isPartiallyPaid(): bool
    {
        return $this->status !== 'paid' && $this->totalPaidAmount() > 0 && $this->balanceDue() > 0;
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

    public function recurringChildren()
    {
        return $this->hasMany(Invoice::class, 'recurring_parent_id');
    }

    public function recurringParent()
    {
        return $this->belongsTo(Invoice::class, 'recurring_parent_id');
    }

    public function getNextRecurringDate(): \Carbon\Carbon
    {
        $date = $this->recurring_next_date ?? $this->due_date ?? now();
        $date = \Carbon\Carbon::parse($date);
        return match($this->recurring_frequency) {
            'weekly'    => $date->copy()->addWeek(),
            'quarterly' => $date->copy()->addMonths(3),
            'yearly'    => $date->copy()->addYear(),
            default     => $date->copy()->addMonth(),
        };
    }
}