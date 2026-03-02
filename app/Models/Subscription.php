<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'company_id', 'plan', 'status', 'auto_renew',
        'trial_ends_at', 'on_trial', 'starts_at', 'ends_at'
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'starts_at'     => 'datetime',
        'ends_at'       => 'datetime',
        'on_trial'      => 'boolean',
        'auto_renew'    => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function isOnTrial(): bool
    {
        return $this->on_trial && $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function isActive(): bool
    {
        if ($this->isOnTrial()) return true;
        return $this->status === 'active' && ($this->ends_at === null || $this->ends_at->isFuture());
    }

    public function canDownloadPdf(): bool
    {
        // Check company bypass first
        if ($this->company->hasBypass()) return true;
        return $this->isOnTrial() || $this->isActive();
    }

    public function daysLeftOnTrial(): int
    {
        if (!$this->isOnTrial()) return 0;
        return (int) now()->diffInDays($this->trial_ends_at);
    }
}