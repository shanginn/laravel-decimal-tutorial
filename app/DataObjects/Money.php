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
    }

    public function subtract(self $another): static
    {}

    public function multiply(self $another): static
    {}

    public function divide(self $another): static
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
