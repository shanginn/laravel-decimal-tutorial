<?php

namespace Tests\Unit;

use App\DataObjects\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function testAdd()
    {
        $left = new Money(1_00);
        $right = new Money(2_00);

        $result = $left->add($right);

        $this->assertEquals(3_00, $result->cents);
    }

    public function testSubtract()
    {
        $left = new Money(1_00);
        $right = new Money(2_00);

        $result = $left->subtract($right);

        $this->assertEquals(-1_00, $result->cents);
    }

    public function testMultiply()
    {
        $left = new Money(1_00);
        $right = 2;

        $result = $left->multiply($right);

        $this->assertEquals(2_00, $result->cents);
    }

    public function testDivideInt()
    {
        $left = new Money(1_00);
        $right = 2;

        [$result, $remainder] = $left->divide($right);

        $this->assertEquals(0_50, $result->cents);
        $this->assertEquals(0, $remainder);
    }

    public function testDivideZero()
    {
        $left = new Money(1_00);
        $right = 0;

        $this->expectException(\DivisionByZeroError::class);

        $left->divide($right);
    }

    public function testDivideCents()
    {
        $left = new Money(1_00);
        $right = 3;

        [$result, $remainder] = $left->divide($right);

        $this->assertEquals(0_33, $result->cents);
        $this->assertEquals(1, $remainder);
    }

    public function testCeil()
    {
        $number = new Money(1_10);
        $result = $number->ceil();
        $this->assertEquals(2_00, $result->cents);

        $number = new Money(1_50);
        $result = $number->ceil();
        $this->assertEquals(2_00, $result->cents);

        $number = new Money(-1_90);
        $result = $number->ceil();
        $this->assertEquals(-1_00, $result->cents);
    }

    public function testFloor()
    {
        $number = new Money(1_90);
        $result = $number->floor();
        $this->assertEquals(1_00, $result->cents);

        $number = new Money(1_50);
        $result = $number->floor();
        $this->assertEquals(1_00, $result->cents);

        $number = new Money(-1_10);
        $result = $number->floor();
        $this->assertEquals(-2_00, $result->cents);
    }

    public function testToString()
    {}
}
