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
     * @return array{0: static, 1: int}
     */
    public function divide(int $divisor): array
    {
        $result = intdiv($this->cents, $divisor);
        $remainder = $this->cents % $divisor;

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
    {}

    public function percent(int $percent): static
    {}

    public function __toString(): string
    {}
}
