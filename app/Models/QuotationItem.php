<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuotationItem extends Model
{
    use HasFactory;

    protected $fillable = [
    'quotation_id', 'catalog_item_id', 'description',
    'quantity', 'unit_price', 'buying_price', 'profit',
    'margin_percentage', 'total_price', 'is_labour','item_type_id',
    ];

    protected $casts = [
        'is_labour' => 'boolean',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
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