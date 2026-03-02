<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatalogItem extends Model
{
    use HasFactory;

    protected $fillable = [
    'company_id', 'service_category_id', 'name',
    'default_unit_price', 'default_buying_price', 'unit_of_measure'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class);
    }
}