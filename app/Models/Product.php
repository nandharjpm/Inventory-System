<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'code',
        'price',
        'tax_percentage',
        'stock_on_hand',
    ];
}
