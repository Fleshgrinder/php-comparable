<?php

declare(strict_types = 1);

namespace Fleshgrinder\Core;

use PHPUnit\Framework\TestCase;

final class NullOrderingTest extends TestCase {
	/**
	 * @testbox Null ordering is not less.
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 * @covers \Fleshgrinder\Core\NullOrdering::isLess
	 */
	public static function testIsLess() {
		static::assertFalse(NullOrdering::new(Ordering::LT)->isLess());
	}

	/**
	 * @testdox Null ordering is not less or equal.
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 * @covers \Fleshgrinder\Core\NullOrdering::isLessOrEqual
	 */
	public static function testIsLessOrEqual() {
		static::assertFalse(NullOrdering::new(Ordering::LT)->isLessOrEqual());
	}

	/**
	 * @testbox Null ordering is not less.
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 * @covers \Fleshgrinder\Core\NullOrdering::isEqual
	 */
	public static function testIsEqual() {
		static::assertFalse(NullOrdering::new(Ordering::EQ)->isEqual());
	}

	/**
	 * @testdox Null ordering is not greater or equal.
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 * @covers \Fleshgrinder\Core\NullOrdering::isGreaterOrEqual
	 */
	public static function testIsGreaterOrEqual() {
		static::assertFalse(NullOrdering::new(Ordering::GT)->isGreaterOrEqual());
	}

	/**
	 * @testbox Null ordering is not greater.
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 * @covers \Fleshgrinder\Core\NullOrdering::isGreater
	 */
	public static function testIsGreater() {
		static::assertFalse(NullOrdering::new(Ordering::GT)->isGreater());
	}

	/**
	 * @testdox Null ordering `then` returns other.
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 * @covers \Fleshgrinder\Core\NullOrdering::then
	 * @uses \Fleshgrinder\Core\Ordering
	 */
	public static function testThen() {
		$ordering = Ordering::Less();

		static::assertSame($ordering, NullOrdering::new(Ordering::EQ)->then($ordering));
	}

	/**
	 * @testdox Null ordering `thenWith` invokes callback.
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 * @covers \Fleshgrinder\Core\NullOrdering::thenWith
	 * @uses \Fleshgrinder\Core\Ordering
	 */
	public static function testThenWith() {
		$ordering = Ordering::Greater();

		static::assertSame($ordering, NullOrdering::new(Ordering::EQ)->thenWith(function () use ($ordering) {
			return $ordering;
		}));
	}

	/**
	 * @testdox Null ordering integer value is outside smaller than less (-1).
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 * @covers \Fleshgrinder\Core\NullOrdering::toInt
	 */
	public static function testToInt() {
		static::assertLessThan(Ordering::LT, NullOrdering::new(Ordering::GT)->toInt());
	}

	/**
	 * @testdox Reversed null ordering is itself.
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 * @covers \Fleshgrinder\Core\NullOrdering::toReverse
	 */
	public static function testToReverse() {
		$ordering = NullOrdering::new(Ordering::EQ);

		static::assertSame($ordering, $ordering->toReverse());
	}

	/**
	 * @testdox Null ordering is always uncomparable.
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::compareTo
	 * @covers \Fleshgrinder\Core\NullOrdering::doCompareTo
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 * @uses \Fleshgrinder\Core\UncomparableException
	 * @expectedException \Fleshgrinder\Core\UncomparableException
	 * @expectedExceptionMessage Cannot compare Fleshgrinder\Core\NullOrdering with Fleshgrinder\Core\NullOrdering
	 */
	public static function testDoCompareTo() {
		$ordering = NullOrdering::new();
		$ordering->compareTo($ordering);
	}
}
