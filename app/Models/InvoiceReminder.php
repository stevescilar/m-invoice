<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceReminder extends Model
{
    protected $fillable = [
        'invoice_id', 'reminder_type', 'scheduled_at', 'sent_at', 'status'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at'      => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}