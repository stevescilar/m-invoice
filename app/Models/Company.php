<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Notification;

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
        'bank_branch',
        'referral_code',
        'referral_count',
        'primary_color',
        'is_bypass',
        'cc_owner_on_emails',
        'notification_email',
        'auto_invoice_reminders',
        'auto_quotation_reminders',
    ];

    protected $casts = [
        'is_bypass'                => 'boolean',
        'cc_owner_on_emails'       => 'boolean',
        'auto_invoice_reminders'   => 'boolean',
        'auto_quotation_reminders' => 'boolean',
    ];

    /**
     * Return deduplicated list of owner/tenant copy email addresses when cc_owner_on_emails is enabled.
     */
    public function getCopyRecipients(?string $excludeToEmail = null, ?User $actingUser = null): array
    {
        if (!($this->cc_owner_on_emails ?? true)) {
            return [];
        }

        $candidates = array_filter([
            $this->notification_email,
            $actingUser?->email,
            $this->owner?->email,
            $this->email,
        ], fn ($email) => !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL));

        $normalizedExclude = $excludeToEmail ? strtolower(trim($excludeToEmail)) : null;
        $unique = [];

        foreach ($candidates as $email) {
            $lower = strtolower(trim($email));
            if ($lower !== $normalizedExclude && !isset($unique[$lower])) {
                $unique[$lower] = trim($email);
            }
        }

        return array_values($unique);
    }

    /**
     * Send a Mailable to a client, automatically BCC-ing the owner/tenant if enabled,
     * with automatic fallback to cPanel's local sendmail transport if SMTP auth is unconfigured/fails.
     */
    public function sendMail(string $toEmail, \Illuminate\Mail\Mailable $mailable, ?User $actingUser = null, bool $includeOwnerCopy = true): void
    {
        $bcc = $includeOwnerCopy ? $this->getCopyRecipients($toEmail, $actingUser) : [];

        try {
            $pending = \Illuminate\Support\Facades\Mail::to($toEmail);
            if (!empty($bcc)) {
                $pending->bcc($bcc);
            }
            $pending->send($mailable);
        } catch (\Throwable $smtpException) {
            \Illuminate\Support\Facades\Log::warning('Primary mailer failed, attempting sendmail fallback: ' . $smtpException->getMessage());
            try {
                $fallback = \Illuminate\Support\Facades\Mail::mailer('sendmail')->to($toEmail);
                if (!empty($bcc)) {
                    $fallback->bcc($bcc);
                }
                $fallback->send($mailable);
            } catch (\Throwable $fallbackException) {
                // Throw original SMTP error so user sees the actionable reason
                throw $smtpException;
            }
        }
    }

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
    public function getReferralUrl(): string
    {
        return url('/register?ref=' . $this->referral_code);
    }
    public function itemTypes()
    {
        return $this->hasMany(\App\Models\ItemType::class)->orderBy('sort_order');
    }
}
