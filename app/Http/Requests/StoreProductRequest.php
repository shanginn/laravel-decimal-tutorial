<?php

namespace App\Http\Requests;

use App\DataObjects\Money;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $title
 * @property float $discount
 * @property Money $price
 */
class StoreProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'discount' => 'nullable|numeric',
            'price' => 'required|numeric|decimal:2|gt:0',
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'price' => Money::fromDecimal($this->price)
        ]);
    }
}
