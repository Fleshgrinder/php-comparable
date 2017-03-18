<?php

declare(strict_types = 1);

namespace Fleshgrinder\Core;

use PHPUnit\Framework\TestCase;

final class NullOrderingTest extends TestCase {
	/**
	 * @testdox is always uncomparable
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::compareTo
	 * @covers \Fleshgrinder\Core\NullOrdering::doCompareTo
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 * @uses   \Fleshgrinder\Core\UncomparableException
	 * @expectedException \Fleshgrinder\Core\UncomparableException
	 * @expectedExceptionMessage Cannot compare Fleshgrinder\Core\NullOrdering with Fleshgrinder\Core\NullOrdering
	 */
	public static function testDoCompareTo() {
		$ordering = NullOrdering::new();
		$ordering->compareTo($ordering);
	}

	/**
	 * @testdox is not equal
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 * @covers \Fleshgrinder\Core\NullOrdering::isEqual
	 */
	public static function testIsEqual() {
		static::assertFalse(NullOrdering::new(Ordering::EQ)->isEqual());
	}

	/**
	 * @testdox is not greater
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 * @covers \Fleshgrinder\Core\NullOrdering::isGreater
	 */
	public static function testIsGreater() {
		static::assertFalse(NullOrdering::new(Ordering::GT)->isGreater());
	}

	/**
	 * @testdox is not greater or equal
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 * @covers \Fleshgrinder\Core\NullOrdering::isGreaterOrEqual
	 */
	public static function testIsGreaterOrEqual() {
		static::assertFalse(NullOrdering::new(Ordering::GT)->isGreaterOrEqual());
	}

	/**
	 * @testdox is not less
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 * @covers \Fleshgrinder\Core\NullOrdering::isLess
	 */
	public static function testIsLess() {
		static::assertFalse(NullOrdering::new(Ordering::LT)->isLess());
	}

	/**
	 * @testdox is not less or equal
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 * @covers \Fleshgrinder\Core\NullOrdering::isLessOrEqual
	 */
	public static function testIsLessOrEqual() {
		static::assertFalse(NullOrdering::new(Ordering::LT)->isLessOrEqual());
	}

	/**
	 * @testdox then returns other
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 * @covers \Fleshgrinder\Core\NullOrdering::then
	 * @uses   \Fleshgrinder\Core\Ordering
	 */
	public static function testThen() {
		$ordering = Ordering::Less();

		static::assertSame($ordering, NullOrdering::new(Ordering::EQ)->then($ordering));
	}

	/**
	 * @testdox thenWith invokes callback
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 * @covers \Fleshgrinder\Core\NullOrdering::thenWith
	 * @uses   \Fleshgrinder\Core\Ordering
	 */
	public static function testThenWith() {
		$ordering = Ordering::Greater();

		static::assertSame($ordering, NullOrdering::new(Ordering::EQ)->thenWith(function () use ($ordering) {
			return $ordering;
		}));
	}

	/**
	 * @testdox integer value is less than less (-1)
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 * @covers \Fleshgrinder\Core\NullOrdering::toInt
	 */
	public static function testToInt() {
		static::assertLessThan(Ordering::LT, NullOrdering::new(Ordering::GT)->toInt());
	}

	/**
	 * @testdox returns another null ordering if reversed
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 * @covers \Fleshgrinder\Core\NullOrdering::toReverse
	 */
	public static function testToReverse() {
		$ordering = NullOrdering::new(Ordering::EQ);
		$reverse  = $ordering->toReverse();

		static::assertInstanceOf(NullOrdering::CLASS, $reverse);
		static::assertNotSame($reverse, $ordering);
	}
}
