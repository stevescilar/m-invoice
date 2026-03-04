<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffInvitation extends Model
{
    protected $fillable = [
        'company_id', 'invited_by', 'email', 'name', 'token', 'status', 'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function invitedBy()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return $this->status === 'pending' && !$this->isExpired();
    }
}