<?php

namespace App\Models;

use App\DataObjects\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Money $price
 * @property float $discount
 * @property-read string $title
 */
class Product extends Model
{
    use HasFactory;

    protected $casts = [
        'price' => Money::class,
    ];

    protected $fillable = [
        'title',
        'price',
        'discount',
    ];
}
