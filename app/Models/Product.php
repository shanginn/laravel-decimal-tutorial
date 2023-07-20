<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use App\DataObjects\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Money $price
 * @property float $discount
 * @property string $title
 * @property-read string $price_with_vat
 * @property-read string $vat_amount
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

    protected $appends = [
        'price_with_vat',
        'vat_amount',
    ];

    protected function priceWithVat(): Attribute
    {
        return Attribute::make(
            get: fn () => (string) $this->price->add(
                Money::fromDecimal($this->vat_amount)
            )
        );
    }

    protected function vatAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => (string) $this->price->percent(
                config('product.vat')
            )[0]
        );
    }
}
