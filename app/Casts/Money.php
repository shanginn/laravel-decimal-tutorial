<?php

namespace App\Casts;

use App\DataObjects\Money as MoneyDataObject;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class Money implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?MoneyDataObject
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof MoneyDataObject) {
            return $value;
        }

        return MoneyDataObject::fromDecimal((string) $value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if (!$value instanceof MoneyDataObject) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid type for field \'%s\' (%s: %s), expected Money',
                $key,
                gettype($value),
                (string) $value
            ));
        }

        return (string) $value;
    }

    public function serialize($model, string $key, $value, array $attributes): string
    {
        return (string) $value;
    }
}
