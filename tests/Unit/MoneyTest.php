<?php

namespace Tests\Unit;

use App\DataObjects\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function testAdd()
    {
        $left = new Money(100);
        $right = new Money(200);

        $result = $left->add($right);

        $this->assertEquals(300, $result->cents);
    }

    public function testSubtract()
    {
        $left = new Money(100);
        $right = new Money(200);

        $result = $left->subtract($right);

        $this->assertEquals(-100, $result->cents);
    }

    public function testMultiply()
    {
        $left = new Money(100);
        $right = 2;

        $result = $left->multiply($right);

        $this->assertEquals(200, $result->cents);
    }

    public function testDivideInt()
    {
        $left = new Money(100);
        $right = 2;

        [$result, $remainder] = $left->divide($right);

        $this->assertEquals(50, $result->cents);
        $this->assertEquals(0, $remainder);
    }

    public function testDivideZero()
    {
        $left = new Money(100);
        $right = 0;

        $this->expectException(\DivisionByZeroError::class);

        $left->divide($right);
    }

    public function testDivideCents()
    {
        $left = new Money(100);
        $right = 3;

        [$result, $remainder] = $left->divide($right);

        $this->assertEquals(33, $result->cents);
        $this->assertEquals(1, $remainder);
    }

    public function testCeil()
    {
        $number = new Money(110);
        $result = $number->ceil();
        $this->assertEquals(200, $result->cents);

        $number = new Money(150);
        $result = $number->ceil();
        $this->assertEquals(200, $result->cents);
    }

    public function testFloor()
    {}

    public function testToString()
    {}
}
