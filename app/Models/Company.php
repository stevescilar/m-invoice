<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo',
        'phone',
        'email',
        'address',
        'signature',
        'footer_message',
        'kra_pin',
        'owner_id',
        'mpesa_paybill',
        'mpesa_account',
        'mpesa_till',
        'mpesa_number',
        'bank_name',
        'bank_account',
        'bank_branch'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function serviceCategories()
    {
        return $this->hasMany(ServiceCategory::class);
    }

    public function hasBypass(): bool
    {
        return (bool) $this->is_bypass;
    }

    public function catalogItems()
    {
        return $this->hasMany(CatalogItem::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function subscriptionTransactions()
    {
        return $this->hasMany(SubscriptionTransaction::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
