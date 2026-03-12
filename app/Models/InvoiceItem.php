<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id', 'catalog_item_id', 'description',
        'quantity', 'unit_price', 'total_price', 'is_labour','item_type_id',
    ];

    protected $casts = [
        'is_labour' => 'boolean',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function catalogItem()
    {
        return $this->belongsTo(CatalogItem::class);
    }

    public function itemType()
    {
        return $this->belongsTo(ItemType::class);
    }
}