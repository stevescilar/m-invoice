<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceDownload extends Model
{
    //
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceDownload extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id', 'company_id', 'downloaded_by', 'charged', 'amount_charged', 'mpesa_code'
    ];

    protected $casts = [
        'charged' => 'boolean',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function downloadedBy()
    {
        return $this->belongsTo(User::class, 'downloaded_by');
    }
}