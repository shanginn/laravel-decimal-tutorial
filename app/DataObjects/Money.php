<?php

declare(strict_types=1);

namespace App\DataObjects;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use App\Casts\Money as MoneyCast;

readonly class Money implements Castable
{
    private const SCALE = 2;

    public function __construct(public int $cents)
    {}

    public static function fromDecimal(string|float|int $decimal): static
    {
        if (!is_numeric($decimal)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid decimal value (%s)',
                $decimal
            ));
        }

        $float = (float) $decimal;
        $rounded = round($float, self::SCALE);

        return new static(
            (int) ($rounded * 10 ** self::SCALE)
        );
    }

    public function add(self $another): static
    {
        return new static($this->cents + $another->cents);
    }

    public function subtract(self $another): static
    {
        return new static($this->cents - $another->cents);
    }

    public function multiply(int $multiplier): static
    {
        return new static($this->cents * $multiplier);
    }

    /**
     * @throws \DivisionByZeroError
     *
     * @return array{0: static, 1: float}
     */
    public function divide(int $divisor): array
    {
        $result = intdiv($this->cents, $divisor);
        $remainder = fmod($this->cents, $divisor);

        return [new static($result), $remainder];
    }

    public function ceil(): static
    {
        $scale = 10 ** self::SCALE;

        return new static(
            (int) (ceil(fdiv($this->cents, $scale)) * $scale)
        );
    }

    public function floor(): static
    {
        $scale = 10 ** self::SCALE;

        return new static(
            (int) (floor(fdiv($this->cents, $scale)) * $scale)
        );
    }

    /**
     * @param float $percent
     * @return array{0: static, 1: float}
     */
    public function percent(float $percent): array
    {
        $total = fdiv($this->cents * $percent, 100);

        $result = (int) floor($total);
        $remainder = fdiv(fmod($total, 100), 100);

        return [
            new static($result),
            $remainder
        ];
    }

    /**
     * @param float $percent
     * @return array{0: static, 1: float}
     */
    public function addPercent(float $percent): array
    {
        $totalPercent = fdiv($this->cents * $percent, 100);
        $total = $this->cents + $totalPercent;

        $result = (int) floor($total);
        $remainder = fdiv(fmod($total, 100), 100);

        return [
            new static($result),
            $remainder
        ];
    }

    /**
     * @param float $percent
     * @return array{0: static, 1: float}
     */
    public function subtractPercent(float $percent): array
    {
        $totalPercent = fdiv($this->cents * $percent, 100);
        $total = $this->cents - $totalPercent;

        $result = (int) floor($total);
        $remainder = fdiv(fmod($total, 100), 100);

        return [
            new static($result),
            $remainder
        ];
    }

    public function __toString(): string
    {
        return number_format(
            $this->cents / 10 ** self::SCALE,
            self::SCALE,
            '.',
            ''
        );
    }

    public function format(
        int $decimals = 2,
        string $decPoint = '.',
        string $thousandsSep = ''
    ): string {
        return number_format(
            $this->cents / 10 ** self::SCALE,
            $decimals,
            $decPoint,
            $thousandsSep
        );
    }

    public static function castUsing(array $arguments): CastsAttributes
    {
        return new MoneyCast();
    }
}
