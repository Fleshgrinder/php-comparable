<?php

declare(strict_types = 1);

namespace Fleshgrinder\Core;

use PHPUnit\Framework\TestCase;

final class OrderingTest extends TestCase {
	public static function provideEqual() {
		return ['Ordering::Equal' => [Ordering::Equal()]];
	}

	public static function provideGreater() {
		return [
			'Ordering::Greater'          => [Ordering::Greater()],
			'Ordering::new(2)'           => [Ordering::new(2)],
			'Ordering::new(PHP_INT_MAX)' => [Ordering::new(\PHP_INT_MAX)],
		];
	}

	public static function provideLess() {
		return [
			'Ordering::new(PHP_INT_MIN)' => [Ordering::new(\PHP_INT_MIN)],
			'Ordering::new(-2)'          => [Ordering::new(-2)],
			'Ordering::Less'             => [Ordering::Less()],
		];
	}

	/**
	 * @testdox is equal if constructed via
	 * @covers       \Fleshgrinder\Core\Ordering::isEqual
	 * @dataProvider provideEqual
	 */
	public static function testIsEqual(Ordering $ordering) {
		static::assertTrue($ordering->isEqual());
	}

	/**
	 * @testdox is greater if constructed via
	 * @covers       \Fleshgrinder\Core\Ordering::isGreater
	 * @dataProvider provideGreater
	 */
	public static function testIsGreater(Ordering $ordering) {
		static::assertTrue($ordering->isGreater());
	}

	/**
	 * @testdox is greater or equal if constructed via
	 * @covers       \Fleshgrinder\Core\Ordering::isGreaterOrEqual
	 * @dataProvider provideEqual
	 * @dataProvider provideGreater
	 */
	public static function testIsGreaterOrEqual(Ordering $ordering) {
		static::assertTrue($ordering->isGreaterOrEqual());
	}

	/**
	 * @testdox is less if constructed via
	 * @covers       \Fleshgrinder\Core\Ordering::isLess
	 * @dataProvider provideLess
	 */
	public static function testIsLess(Ordering $ordering) {
		static::assertTrue($ordering->isLess());
	}

	/**
	 * @testdox is less or equal if constructed via
	 * @covers       \Fleshgrinder\Core\Ordering::isLessOrEqual
	 * @dataProvider provideEqual
	 * @dataProvider provideLess
	 */
	public static function testIsLessOrEqual(Ordering $ordering) {
		static::assertTrue($ordering->isLessOrEqual());
	}

	/**
	 * @testdox is not equal if constructed via
	 * @covers       \Fleshgrinder\Core\Ordering::isEqual
	 * @dataProvider provideGreater
	 * @dataProvider provideLess
	 */
	public static function testIsNotEqual(Ordering $ordering) {
		static::assertFalse($ordering->isEqual());
	}

	/**
	 * @testdox is not greater if constructed via
	 * @covers       \Fleshgrinder\Core\Ordering::isGreater
	 * @dataProvider provideEqual
	 * @dataProvider provideLess
	 */
	public static function testIsNotGreater(Ordering $ordering) {
		static::assertFalse($ordering->isGreater());
	}

	/**
	 * @testdox is not greater or equal if constructed via
	 * @covers       \Fleshgrinder\Core\Ordering::isGreaterOrEqual
	 * @dataProvider provideLess
	 */
	public static function testIsNotGreaterOrEqual(Ordering $ordering) {
		static::assertFalse($ordering->isGreaterOrEqual());
	}

	/**
	 * @testdox is not less if constructed via
	 * @covers       \Fleshgrinder\Core\Ordering::isLess
	 * @dataProvider provideEqual
	 * @dataProvider provideGreater
	 */
	public static function testIsNotLess(Ordering $ordering) {
		static::assertFalse($ordering->isLess());
	}

	/**
	 * @testdox is not less or equal if constructed via
	 * @covers       \Fleshgrinder\Core\Ordering::isLessOrEqual
	 * @dataProvider provideGreater
	 */
	public static function testIsNotLessOrEqual(Ordering $ordering) {
		static::assertFalse($ordering->isLessOrEqual());
	}

	/**
	 * @testdox then returns other if equal
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @covers \Fleshgrinder\Core\Ordering::Equal
	 * @covers \Fleshgrinder\Core\Ordering::new
	 * @covers \Fleshgrinder\Core\Ordering::then
	 */
	public static function testThenEqual() {
		$other = Ordering::new(42);

		static::assertSame($other, Ordering::Equal()->then($other));
	}

	/**
	 * @testdox then returns self if greater
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @covers \Fleshgrinder\Core\Ordering::Greater
	 * @covers \Fleshgrinder\Core\Ordering::Less
	 * @covers \Fleshgrinder\Core\Ordering::new
	 * @covers \Fleshgrinder\Core\Ordering::then
	 */
	public static function testThenGreater() {
		$gt = Ordering::Greater();

		static::assertSame($gt, $gt->then(Ordering::Less()));
	}

	/**
	 * @testdox then returns self if less
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @covers \Fleshgrinder\Core\Ordering::Greater
	 * @covers \Fleshgrinder\Core\Ordering::Less
	 * @covers \Fleshgrinder\Core\Ordering::new
	 * @covers \Fleshgrinder\Core\Ordering::then
	 */
	public static function testThenLess() {
		$lt = Ordering::Less();

		static::assertSame($lt, $lt->then(Ordering::Greater()));
	}

	/**
	 * @testdox then with invokes callback if equal
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @covers \Fleshgrinder\Core\Ordering::Equal
	 * @covers \Fleshgrinder\Core\Ordering::new
	 * @covers \Fleshgrinder\Core\Ordering::thenWith
	 */
	public static function testThenWithEqual() {
		$other = Ordering::new(42);

		static::assertSame($other, Ordering::Equal()->thenWith(function () use ($other) {
			return $other;
		}));
	}

	/**
	 * @testdox then with returns self if greater
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @covers \Fleshgrinder\Core\Ordering::Greater
	 * @covers \Fleshgrinder\Core\Ordering::new
	 * @covers \Fleshgrinder\Core\Ordering::thenWith
	 */
	public static function testThenWithGreater() {
		$gt = Ordering::Greater();

		static::assertSame($gt, $gt->thenWith(function () { }));
	}

	/**
	 * @testdox then with returns self if less
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @covers \Fleshgrinder\Core\Ordering::Less
	 * @covers \Fleshgrinder\Core\Ordering::new
	 * @covers \Fleshgrinder\Core\Ordering::thenWith
	 */
	public static function testThenWithLess() {
		$lt = Ordering::Less();

		static::assertSame($lt, $lt->thenWith(function () { }));
	}

	public static function provideIntData() {
		return [
			'PHP_INT_MIN' => [\PHP_INT_MIN],
			'integer -2'  => [-2],
			'integer -1'  => [-1],
			'integer 0'   => [0],
			'integer 1'   => [1],
			'integer 2'   => [2],
			'PHP_INT_MAX' => [\PHP_INT_MAX],
		];
	}

	/**
	 * @testdox integer value is always in [-1, 1] even if created with
	 * @covers       \Fleshgrinder\Core\Ordering::__construct
	 * @covers       \Fleshgrinder\Core\Ordering::new
	 * @covers       \Fleshgrinder\Core\Ordering::toInt
	 * @dataProvider provideIntData
	 */
	public static function testToInt(int $int) {
		$ord = Ordering::new($int)->toInt();

		static::assertTrue(Ordering::LT <= $ord && $ord <= Ordering::GT);
	}

	/**
	 * @testdox equal stays equal if reversed
	 * @covers  \Fleshgrinder\Core\Ordering::__construct
	 * @covers  \Fleshgrinder\Core\Ordering::isEqual
	 * @covers  \Fleshgrinder\Core\Ordering::Equal
	 * @covers  \Fleshgrinder\Core\Ordering::toReverse
	 * @depends testIsEqual
	 * @depends testIsNotEqual
	 */
	public static function testToReverseEqual() {
		static::assertTrue(Ordering::Equal()->toReverse()->isEqual());
	}

	/**
	 * @testdox greater becomes less if reversing
	 * @covers       \Fleshgrinder\Core\Ordering::isLess
	 * @covers       \Fleshgrinder\Core\Ordering::toReverse
	 * @dataProvider provideGreater
	 * @depends      testIsLess
	 * @depends      testIsNotLess
	 */
	public static function testToReverseGreater(Ordering $ordering) {
		static::assertTrue($ordering->toReverse()->isLess());
	}

	/**
	 * @testdox less becomes greater if reversing
	 * @covers       \Fleshgrinder\Core\Ordering::__construct
	 * @covers       \Fleshgrinder\Core\Ordering::isGreater
	 * @covers       \Fleshgrinder\Core\Ordering::Less
	 * @covers       \Fleshgrinder\Core\Ordering::toReverse
	 * @dataProvider provideLess
	 * @depends      testIsGreater
	 * @depends      testIsNotGreater
	 */
	public static function testToReverseLess(Ordering $ordering) {
		static::assertTrue($ordering->toReverse()->isGreater());
	}

	public static function provideComparableData() {
		return [
			'Less == PHP_INT_MIN'    => [Ordering::Equal(),   Ordering::Less(),    Ordering::new(\PHP_INT_MIN)],
			'Less == LT - 1'         => [Ordering::Equal(),   Ordering::Less(),    Ordering::new(Ordering::LT - 1)],
			'Less == Less'           => [Ordering::Equal(),   Ordering::Less(),    Ordering::Less()],
			'Less < Equal'           => [Ordering::Less(),    Ordering::Less(),    Ordering::Equal()],
			'Less < Greater'         => [Ordering::Less(),    Ordering::Less(),    Ordering::Greater()],
			'Less < GT + 1'          => [Ordering::Less(),    Ordering::Less(),    Ordering::new(Ordering::GT + 1)],
			'Less < PHP_INT_MAX'     => [Ordering::Less(),    Ordering::Less(),    Ordering::new(\PHP_INT_MAX)],

			'Equal > PHP_INT_MIN'    => [Ordering::Greater(), Ordering::Equal(),   Ordering::new(\PHP_INT_MIN)],
			'Equal > LT - 1'         => [Ordering::Greater(), Ordering::Equal(),   Ordering::new(Ordering::LT - 1)],
			'Equal > Less'           => [Ordering::Greater(), Ordering::Equal(),   Ordering::Less()],
			'Equal == Equal'         => [Ordering::Equal(),   Ordering::Equal(),   Ordering::Equal()],
			'Equal < Greater'        => [Ordering::Less(),    Ordering::Equal(),   Ordering::Greater()],
			'Equal < GT + 1'         => [Ordering::Less(),    Ordering::Equal(),   Ordering::new(Ordering::GT + 1)],
			'Equal < PHP_INT_MAX'    => [Ordering::Less(),    Ordering::Equal(),   Ordering::new(\PHP_INT_MAX)],

			'Greater > PHP_INT_MIN'  => [Ordering::Greater(), Ordering::Greater(), Ordering::new(\PHP_INT_MIN)],
			'Greater > LT - 1'       => [Ordering::Greater(), Ordering::Greater(), Ordering::new(Ordering::LT - 1)],
			'Greater > Less'         => [Ordering::Greater(), Ordering::Greater(), Ordering::Less()],
			'Greater > Equal'        => [Ordering::Greater(), Ordering::Greater(), Ordering::Equal()],
			'Greater == Greater'     => [Ordering::Equal(),   Ordering::Greater(), Ordering::Greater()],
			'Greater == GT + 1'      => [Ordering::Equal(),   Ordering::Greater(), Ordering::new(Ordering::GT + 1)],
			'Greater == PHP_INT_MAX' => [Ordering::Equal(),   Ordering::Greater(), Ordering::new(\PHP_INT_MAX)],
		];
	}

	/**
	 * @testdox is comparable to Ordering where
	 * @covers \Fleshgrinder\Core\Ordering::compareTo
	 * @covers \Fleshgrinder\Core\Ordering::doCompareTo
	 * @covers \Fleshgrinder\Core\Ordering::toInt
	 * @dataProvider provideComparableData
	 */
	public static function testDoCompareTo(Ordering $expected, Ordering $self, Ordering $other) {
		static::assertEquals($expected, $self->compareTo($other));
	}

	public static function provideComparableIntData() {
		return [
			'Less == PHP_INT_MIN'    => [Ordering::Equal(),   Ordering::Less(),    \PHP_INT_MIN],
			'Less == LT - 1'         => [Ordering::Equal(),   Ordering::Less(),    Ordering::LT - 1],
			'Less == LT'             => [Ordering::Equal(),   Ordering::Less(),    Ordering::LT],
			'Less < EQ'              => [Ordering::Less(),    Ordering::Less(),    Ordering::EQ],
			'Less < GT'              => [Ordering::Less(),    Ordering::Less(),    Ordering::GT],
			'Less < GT + 1'          => [Ordering::Less(),    Ordering::Less(),    Ordering::GT + 1],
			'Less < PHP_INT_MAX'     => [Ordering::Less(),    Ordering::Less(),    \PHP_INT_MAX],

			'Equal > PHP_INT_MIN'    => [Ordering::Greater(), Ordering::Equal(),   \PHP_INT_MIN],
			'Equal > LT - 1'         => [Ordering::Greater(), Ordering::Equal(),   Ordering::LT - 1],
			'Equal > LT'             => [Ordering::Greater(), Ordering::Equal(),   Ordering::LT],
			'Equal == EQ'            => [Ordering::Equal(),   Ordering::Equal(),   Ordering::EQ],
			'Equal < GT'             => [Ordering::Less(),    Ordering::Equal(),   Ordering::GT],
			'Equal < GT + 1'         => [Ordering::Less(),    Ordering::Equal(),   Ordering::GT + 1],
			'Equal < PHP_INT_MAX'    => [Ordering::Less(),    Ordering::Equal(),   \PHP_INT_MAX],

			'Greater > PHP_INT_MIN'  => [Ordering::Greater(), Ordering::Greater(), \PHP_INT_MIN],
			'Greater > LT - 1'       => [Ordering::Greater(), Ordering::Greater(), Ordering::LT - 1],
			'Greater > LT'           => [Ordering::Greater(), Ordering::Greater(), Ordering::LT],
			'Greater > EQ'           => [Ordering::Greater(), Ordering::Greater(), Ordering::EQ],
			'Greater == GT'          => [Ordering::Equal(),   Ordering::Greater(), Ordering::GT],
			'Greater == GT + 1'      => [Ordering::Equal(),   Ordering::Greater(), Ordering::GT + 1],
			'Greater == PHP_INT_MAX' => [Ordering::Equal(),   Ordering::Greater(), \PHP_INT_MAX],
		];
	}

	/**
	 * @testdox is comparable to integer where
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @covers \Fleshgrinder\Core\Ordering::compareTo
	 * @covers \Fleshgrinder\Core\Ordering::doCompareTo
	 * @covers \Fleshgrinder\Core\Ordering::toInt
	 * @dataProvider provideComparableIntData
	 */
	public static function testDoCompareToInt(Ordering $expected, Ordering $self, int $other) {
		static::assertEquals($expected, $self->compareTo($other));
	}

	public static function provideUncomparableData() {
		return [
			Value::TYPE_ARRAY    => [[]],
			Value::TYPE_BOOL     => [\true],
			Value::TYPE_CALLABLE => [function () { }],
			Value::TYPE_FLOAT    => [1.2],
			Value::TYPE_ITERABLE => [(function () { yield 1; })()],
			Value::TYPE_NULL     => [\null],
			Value::TYPE_OBJECT   => [(object) []],
			\DateTime::CLASS     => [new \DateTime],
			Value::TYPE_RESOURCE => [\fopen('php://memory', 'rb')],
			Value::TYPE_STRING   => ['test'],
		];
	}

	/**
	 * @testdox is not comparable to other types, e.g.
	 * @covers       \Fleshgrinder\Core\Ordering::__construct
	 * @covers       \Fleshgrinder\Core\Ordering::new
	 * @covers       \Fleshgrinder\Core\Ordering::compareTo
	 * @covers       \Fleshgrinder\Core\Ordering::doCompareTo
	 * @uses         \Fleshgrinder\Core\NullOrdering
	 * @uses         \Fleshgrinder\Core\UncomparableException
	 * @dataProvider provideUncomparableData
	 * @expectedException \Fleshgrinder\Core\UncomparableException
	 * @expectedExceptionMessage Cannot compare Fleshgrinder\Core\Ordering with
	 */
	public static function testDoCompareToUncomparable($other) {
		Ordering::new(42)->compareTo($other);
	}
}
