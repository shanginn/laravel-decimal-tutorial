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

        [$result, $centsRemainder] = $left->divide($right);

        $this->assertEquals(50, $result->cents);
        $this->assertEquals(0, $centsRemainder);
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

        [$result, $centsRemainder] = $left->divide($right);

        $this->assertEquals(33, $result->cents);
        $this->assertEquals(1.0, $centsRemainder);

        $left = new Money(100_15);
        $right = 1000;

        [$result, $centsRemainder] = $left->divide($right);
        $this->assertEquals(10, $result->cents);
        $this->assertEquals(15.0, $centsRemainder);
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

    public function testPercent()
    {
        $number = new Money(100_15);
        [$result, $centsRemainder] = $number->percent(10);
        $this->assertEquals(10_01, $result->cents);
        $this->assertEquals(0.5, $centsRemainder);

        $number = new Money(111_11);
        [$result, $centsRemainder] = $number->percent(33);
        $this->assertEquals(36_66, $result->cents);
        $this->assertEquals(0.63, $centsRemainder);

        $number = new Money(123_45);
        [$result, $centsRemainder] = $number->percent(99.9999);
        $this->assertEquals(123_44, $result->cents);
        $this->assertGreaterThan(0, $centsRemainder);
        $this->assertLessThan(1, $centsRemainder);

        $number = new Money(123_45);
        [$result, $centsRemainder] = $number->percent(0.0001);
        $this->assertEquals(0, $result->cents);
        $this->assertLessThan(1, $centsRemainder);
        $this->assertGreaterThan(0, $centsRemainder);
    }

    public function testToString()
    {}
}
