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
    {}

    public function divide(int $divisor): static
    {}

    public function round(int $precision): static
    {}

    public function floor(): static
    {}

    public function percent(int $percent): static
    {}

    public function __toString(): string
    {}
}
