<?php

declare(strict_types=1);

namespace App\DataObjects;

readonly class Money
{
    private const SCALE = 2;

    public function __construct(public int $cents)
    {}

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
            (int) (ceil($this->cents / $scale) * $scale)
        );
    }

    public function floor(): static
    {
        $scale = 10 ** self::SCALE;

        return new static(
            (int) (floor($this->cents / $scale) * $scale)
        );
    }

    /**
     * @param float $percent
     * @return array{0: static, 1: float}
     */
    public function percent(float $percent): array
    {
        $scale = 10 ** self::SCALE;

        $total = $this->cents * $percent;

        $result = (int) floor($total / $scale);
        $remainder = fmod($total, $scale) / $scale;

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
}
