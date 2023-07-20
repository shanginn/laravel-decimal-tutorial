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
    {}

    public function testMultiply()
    {}

    public function testDivide()
    {}

    public function testRound()
    {}

    public function testFloor()
    {}

    public function testToString()
    {}
}
