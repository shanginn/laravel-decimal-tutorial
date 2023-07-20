<?php

namespace Tests\Unit;

use App\DataObjects\Money;
use PHPUnit\Framework\Attributes\DataProvider;
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

    public function testDivideZero()
    {
        $left = new Money(1_00);
        $right = 0;

        $this->expectException(\DivisionByZeroError::class);

        $left->divide($right);
    }

    public static function divideDataProvider(): \Generator
    {
        yield 'Деление без остатка' => [1_00, 2, 50, 0.0];
        yield 'Остаток в одну копейку' => [1_00, 3, 33, 1.0];
        yield 'Остаток в 15 копеек' => [100_15, 1000, 10, 15.0];
    }

    #[DataProvider('divideDataProvider')]
    public function testDivide(int $cents, int $divisor, int $expectedResult, float $expectedRemainder)
    {
        $left = new Money($cents);

        [$result, $centsRemainder] = $left->divide($divisor);

        $this->assertEquals($expectedResult, $result->cents);
        $this->assertEquals($expectedRemainder, $centsRemainder);
    }

    public static function ceilDataProvider(): \Generator
    {
        yield 'Округление в большую сторону (1.1 > 2)' => [1_10, 2_00];
        yield 'Округление в большую сторону (1.5 > 2)' => [1_50, 2_00];
        yield 'Округление в большую сторону (-1.9 > -1)' => [-1_90, -1_00];
    }

    #[DataProvider('ceilDataProvider')]
    public function testCeil(int $cents, int $expectedResult)
    {
        $number = new Money($cents);
        $result = $number->ceil();
        $this->assertEquals($expectedResult, $result->cents);
    }

    public static function floorDataProvider(): \Generator
    {
        yield 'Округление в меньшую сторону (1.1 > 1)' => [1_10, 1_00];
        yield 'Округление в меньшую сторону (1.5 > 1)' => [1_50, 1_00];
        yield 'Округление в меньшую сторону (-1.9 > -2)' => [-1_90, -2_00];
    }

    #[DataProvider('floorDataProvider')]
    public function testFloor(int $cents, int $expectedResult)
    {
        $number = new Money($cents);
        $result = $number->floor();
        $this->assertEquals($expectedResult, $result->cents);
    }

    public static function percentDataProvider(): \Generator
    {
        yield '10% от 100' => [100_00, 10, 10_00, true];
        yield '10% от 100.15' => [100_15, 10, 10_01, false];
        yield '33% от 111.11' => [111_11, 33, 36_66, false];
        yield '99.9999% от 123.45' => [123_45, 99.9999, 123_44, false];
        yield '0.0001% от 123.45' => [123_45, 0.0001, 0, false];
    }

    #[DataProvider('percentDataProvider')]
    public function testPercent(int $cents, float $percent, int $expectedResult, bool $emptyRemainder)
    {
        $number = new Money($cents);
        [$result, $centsRemainder] = $number->percent($percent);

        $this->assertEquals($expectedResult, $result->cents);
        $this->assertEquals($emptyRemainder, $centsRemainder === 0.0);
    }

    public static function addPercentDataProvider(): \Generator
    {
        yield '100 + 10%' => [100_00, 10, 110_00, true];
        yield '100.15 + 10%' => [100_15, 10, 110_16, false];
        yield '111.11 + 33%' => [111_11, 33, 147_77, false];
        yield '123.45 + 99.9999%' => [123_45, 99.9999, 246_89, false];
        yield '123.45 + 0.0001%' => [123_45, 0.0001, 123_45, false];
    }

    #[DataProvider('addPercentDataProvider')]
    public function testAddPercent(int $cents, float $percent, int $expectedResult, bool $emptyRemainder)
    {
        $number = new Money($cents);
        [$result, $centsRemainder] = $number->addPercent($percent);

        $this->assertEquals($expectedResult, $result->cents);
        $this->assertEquals($emptyRemainder, $centsRemainder === 0.0);
    }

    public static function subtractPercentDataProvider(): \Generator
    {
        yield '100 - 10%' => [100_00, 10, 90_00, true];
        yield '100.15 - 10%' => [100_15, 10, 90_13, false];
        yield '111.11 - 33%' => [111_11, 33, 74_44, false];
        yield '123.45 - 99.9999%' => [123_45, 99.9999, 0, false];
        yield '123.45 - 0.0001%' => [123_45, 0.0001, 123_44, false];
    }

    #[DataProvider('subtractPercentDataProvider')]
    public function testSubtractPercent(int $cents, float $percent, int $expectedResult, bool $emptyRemainder)
    {
        $number = new Money($cents);
        [$result, $centsRemainder] = $number->subtractPercent($percent);

        $this->assertEquals($expectedResult, $result->cents);
        $this->assertEquals($emptyRemainder, $centsRemainder === 0.0);
    }

    public static function toStringDataProvider(): \Generator
    {
        yield [1_00, '1.00'];
        yield [1_000_99, '1000.99'];
        yield [-1_00, '-1.00'];
        yield [1, '0.01'];
        yield [0, '0.00'];
    }

    #[DataProvider('toStringDataProvider')]
    public function testToString(int $cents, string $expectedResult)
    {
        $number = new Money($cents);
        $this->assertEquals($expectedResult, (string) $number);
    }

    public static function fromDecimalDataProvider(): \Generator
    {
        yield ['1.00', 1_00];
        yield ['1.000', 1_00];
        yield ['1.001', 1_00];
        yield ['0.00', 0];
        yield ['0.01', 1];
        yield ['0.001', 0];
        yield ['100.11', 100_11];
        yield ['-100', -100_00];
    }

    #[DataProvider('fromDecimalDataProvider')]
    public function testFromDecimal(string $decimal, int $expectedCents)
    {
        $number = Money::fromDecimal($decimal);
        $this->assertEquals($expectedCents, $number->cents);
    }

    public static function fromWrongDecimalDataProvider(): \Generator
    {
        yield 'Word' => [fake()->word];
        yield 'Wrong delimiter' => ['1,00'];
        yield '1.00.00' => ['1.00.00'];
    }

    #[DataProvider('fromWrongDecimalDataProvider')]
    public function testFromWrongDecimal(string $decimal)
    {
        $this->expectException(\InvalidArgumentException::class);
        Money::fromDecimal($decimal);
    }
}
