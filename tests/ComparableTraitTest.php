<?php

declare(strict_types = 1);

namespace Fleshgrinder\Core;

use PHPUnit\Framework\TestCase;

final class ComparableTraitTest extends TestCase {
	/**
	 * @testdox ::getComparator throws an AssertionError if using class does not implement the Comparable interface
	 * @covers \Fleshgrinder\Core\ComparableTrait::getComparator
	 * @expectedException \AssertionError
	 */
	public static function testGetComparatorAssertionError() {
		(new class {
			use ComparableTrait;
		})::getComparator();
	}

	/**
	 * @testdox ::getComparator comparator throws an UncomparableException if the left-hand side is not an instance of itself
	 * @covers \Fleshgrinder\Core\ComparableTrait::getComparator
	 * @uses \Fleshgrinder\Core\Comparators\ComparatorDelegate
	 * @uses \Fleshgrinder\Core\UncomparableException
	 * @expectedException \Fleshgrinder\Core\UncomparableException
	 * @expectedExceptionMessage Cannot compare Fleshgrinder\Core\ComparableFake with null
	 */
	public function testGetComparatorUncomparableException() {
		ComparableFake::getComparator()(\null, \null);
	}

	/**
	 * @testdox ::getComparator comparator calls ::compareTo of left-hand side
	 * @covers \Fleshgrinder\Core\ComparableTrait::compareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::doCompareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::getComparator
	 * @uses \Fleshgrinder\Core\Comparators\ComparatorDelegate
	 * @uses \Fleshgrinder\Core\NullOrdering
	 * @uses \Fleshgrinder\Core\Ordering
	 * @uses \Fleshgrinder\Core\UncomparableException
	 * @expectedException \Fleshgrinder\Core\UncomparableException
	 * @expectedExceptionMessage Cannot compare Fleshgrinder\Core\ComparableFake with null
	 */
	public function testGetComparatorLhsCall() {
		ComparableFake::getComparator()(new ComparableFake, \null);
	}

	/**
	 * @testdox user sorting with ::getComparator
	 * @covers \Fleshgrinder\Core\ComparableTrait::compareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::doCompareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::getComparator
	 * @covers \Fleshgrinder\Core\Comparators\ArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\Comparator::compare
	 * @covers \Fleshgrinder\Core\Comparators\ComparatorDelegate::__construct
	 * @covers \Fleshgrinder\Core\Comparators\ComparatorDelegate::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\ComparatorDelegate::checkCallable
	 * @covers \Fleshgrinder\Core\Comparators\ComparatorDelegate::new
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @covers \Fleshgrinder\Core\Ordering::new
	 * @covers \Fleshgrinder\Core\Ordering::toInt
	 */
	public static function testGetComparator() {
		$x = new ComparableFake(1);
		$y = new ComparableFake(2);
		$z = new ComparableFake(3);

		$data = [$z, $y, $x];

		\usort($data, ComparableFake::getComparator());

		static::assertSame([$x, $y, $z], $data);
	}

	/**
	 * @testdox user sorting with ::getReverseComparator
	 * @covers \Fleshgrinder\Core\ComparableTrait::compareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::doCompareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::getComparator
	 * @covers \Fleshgrinder\Core\ComparableTrait::getReverseComparator
	 * @covers \Fleshgrinder\Core\Comparators\ArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\Comparator::compare
	 * @covers \Fleshgrinder\Core\Comparators\ComparatorDelegate::__construct
	 * @covers \Fleshgrinder\Core\Comparators\ComparatorDelegate::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\ComparatorDelegate::checkCallable
	 * @covers \Fleshgrinder\Core\Comparators\ComparatorDelegate::new
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\ReverseComparator::__construct
	 * @covers \Fleshgrinder\Core\Comparators\ReverseComparator::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\ReverseComparator::new
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @covers \Fleshgrinder\Core\Ordering::new
	 * @covers \Fleshgrinder\Core\Ordering::toInt
	 */
	public static function testGetReverseComparator() {
		$x = new ComparableFake(1);
		$y = new ComparableFake(2);
		$z = new ComparableFake(3);

		$data = [$x, $y, $z];

		\usort($data, ComparableFake::getReverseComparator());

		static::assertSame([$z, $y, $x], $data);
	}

	public static function provideIsLessThanData() {
		return [
			'-1 < 0 == true'  => [\true,  new ComparableFake(-1), new ComparableFake(0)],
			' 0 < 0 == false' => [\false, new ComparableFake( 0), new ComparableFake(0)],
			' 1 < 0 == false' => [\false, new ComparableFake( 1), new ComparableFake(0)],
		];
	}

	/**
	 * @testdox ::isLessThan correctly handles
	 * @covers \Fleshgrinder\Core\ComparableTrait::doCompareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::isLessThan
	 * @covers \Fleshgrinder\Core\Comparators\ArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\Comparator::compare
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @covers \Fleshgrinder\Core\Ordering::isLess
	 * @covers \Fleshgrinder\Core\Ordering::new
	 * @dataProvider provideIsLessThanData
	 */
	public static function testIsLessThan(bool $expected, Comparable $comparable, $other) {
		static::assertSame($expected, $comparable->isLessThan($other));
	}

	/**
	 * @testdox ::isLessThan does not throw an exception on type mismatch
	 * @covers \Fleshgrinder\Core\ComparableTrait::doCompareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::isLessThan
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::isLess
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 */
	public static function testIsLessThanTypeMismatch() {
		static::assertFalse((new ComparableFake)->isLessThan(42));
	}

	/**
	 * @testdox ::isLessThan catches exceptions and always returns a boolean
	 * @covers \Fleshgrinder\Core\ComparableTrait::doCompareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::isLessThan
	 * @covers \Fleshgrinder\Core\Comparators\ArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\Comparator::compare
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::isLess
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 * @covers \Fleshgrinder\Core\UncomparableException::fromIncompatibleTypes
	 */
	public static function testIsLessThanExceptionCatching() {
		static::assertFalse((new ComparableFake)->isLessThan(new ComparableFake(42)));
	}

	public static function provideIsLessThanOrEqualsData() {
		return [
			'-1 <= 0 == true'  => [\true,  new ComparableFake(-1), new ComparableFake(0)],
			' 0 <= 0 == true'  => [\true,  new ComparableFake( 0), new ComparableFake(0)],
			' 1 <= 0 == false' => [\false, new ComparableFake( 1), new ComparableFake(0)],
		];
	}

	/**
	 * @testdox ::isLessThanOrEquals correctly handles
	 * @covers \Fleshgrinder\Core\ComparableTrait::doCompareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::isLessThanOrEquals
	 * @covers \Fleshgrinder\Core\Comparators\ArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\Comparator::compare
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @covers \Fleshgrinder\Core\Ordering::isLessOrEqual
	 * @covers \Fleshgrinder\Core\Ordering::new
	 * @dataProvider provideIsLessThanOrEqualsData
	 */
	public static function testIsLessThanOrEquals(bool $expected, Comparable $comparable, $other) {
		static::assertSame($expected, $comparable->isLessThanOrEquals($other));
	}

	/**
	 * @testdox ::isLessThanOrEquals does not throw an exception on type mismatch
	 * @covers \Fleshgrinder\Core\ComparableTrait::doCompareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::isLessThanOrEquals
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::isLessOrEqual
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 */
	public static function testIsLessThanOrEqualsTypeMismatch() {
		static::assertFalse((new ComparableFake)->isLessThanOrEquals(42));
	}

	/**
	 * @testdox ::isLessThanOrEquals catches exceptions and always returns a boolean
	 * @covers \Fleshgrinder\Core\ComparableTrait::doCompareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::isLessThanOrEquals
	 * @covers \Fleshgrinder\Core\Comparators\ArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\Comparator::compare
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::isLessOrEqual
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 * @covers \Fleshgrinder\Core\UncomparableException::fromIncompatibleTypes
	 */
	public static function testIsLessThanOrEqualsExceptionCatching() {
		static::assertFalse((new ComparableFake)->isLessThanOrEquals(new ComparableFake(42)));
	}

	public static function provideEqualsData() {
		return [
			'-1 == 0 == false' => [\false, new ComparableFake(-1), new ComparableFake(0)],
			' 0 == 0 == true'  => [\true,  new ComparableFake( 0), new ComparableFake(0)],
			' 1 == 0 == false' => [\false, new ComparableFake( 1), new ComparableFake(0)],
		];
	}

	/**
	 * @testdox ::equals correctly handles
	 * @covers \Fleshgrinder\Core\ComparableTrait::doCompareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::equals
	 * @covers \Fleshgrinder\Core\Comparators\ArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\Comparator::compare
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @covers \Fleshgrinder\Core\Ordering::isEqual
	 * @covers \Fleshgrinder\Core\Ordering::new
	 * @dataProvider provideEqualsData
	 */
	public static function testEquals(bool $expected, Comparable $comparable, $other) {
		static::assertSame($expected, $comparable->equals($other));
	}

	/**
	 * @testdox ::equals does not throw an exception on type mismatch
	 * @covers \Fleshgrinder\Core\ComparableTrait::doCompareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::equals
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::isEqual
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 */
	public static function testEqualsTypeMismatch() {
		static::assertFalse((new ComparableFake)->equals(42));
	}

	/**
	 * @testdox ::equals catches exceptions and always returns a boolean
	 * @covers \Fleshgrinder\Core\ComparableTrait::doCompareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::equals
	 * @covers \Fleshgrinder\Core\Comparators\ArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\Comparator::compare
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::isEqual
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 * @covers \Fleshgrinder\Core\UncomparableException::fromIncompatibleTypes
	 */
	public static function testEqualsExceptionCatching() {
		static::assertFalse((new ComparableFake)->equals(new ComparableFake(42)));
	}

	public static function provideIsGreaterThanOrEqualsData() {
		return [
			'-1 >= 0 == false' => [\false, new ComparableFake(-1), new ComparableFake(0)],
			' 0 >= 0 == true'  => [\true,  new ComparableFake( 0), new ComparableFake(0)],
			' 1 >= 0 == true'  => [\true,  new ComparableFake( 1), new ComparableFake(0)],
		];
	}

	/**
	 * @testdox ::isGreaterThanOrEquals correctly handles
	 * @covers \Fleshgrinder\Core\ComparableTrait::doCompareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::isGreaterThanOrEquals
	 * @covers \Fleshgrinder\Core\Comparators\ArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\Comparator::compare
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @covers \Fleshgrinder\Core\Ordering::isGreaterOrEqual
	 * @covers \Fleshgrinder\Core\Ordering::new
	 * @dataProvider provideIsGreaterThanOrEqualsData
	 */
	public static function testIsGreaterThanOrEquals(bool $expected, Comparable $comparable, $other) {
		static::assertSame($expected, $comparable->isGreaterThanOrEquals($other));
	}

	/**
	 * @testdox ::isGreaterThanOrEquals does not throw an exception on type mismatch
	 * @covers \Fleshgrinder\Core\ComparableTrait::doCompareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::isGreaterThanOrEquals
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::isGreaterOrEqual
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 */
	public static function testIsGreaterThanOrEqualsTypeMismatch() {
		static::assertFalse((new ComparableFake)->isGreaterThanOrEquals(42));
	}

	/**
	 * @testdox ::isGreaterThanOrEquals catches exceptions and always returns a boolean
	 * @covers \Fleshgrinder\Core\ComparableTrait::doCompareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::isGreaterThanOrEquals
	 * @covers \Fleshgrinder\Core\Comparators\ArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\Comparator::compare
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::isGreaterOrEqual
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 * @covers \Fleshgrinder\Core\UncomparableException::fromIncompatibleTypes
	 */
	public static function testIsGreaterThanOrEqualsExceptionCatching() {
		static::assertFalse((new ComparableFake)->isGreaterThanOrEquals(new ComparableFake(42)));
	}

	public static function provideIsGreaterThanData() {
		return [
			'-1 > 0 == false' => [\false, new ComparableFake(-1), new ComparableFake(0)],
			' 0 > 0 == false' => [\false, new ComparableFake( 0), new ComparableFake(0)],
			' 1 > 0 == true'  => [\true,  new ComparableFake( 1), new ComparableFake(0)],
		];
	}

	/**
	 * @testdox ::isGreaterThan correctly handles
	 * @covers \Fleshgrinder\Core\ComparableTrait::doCompareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::isGreaterThan
	 * @covers \Fleshgrinder\Core\Comparators\ArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\Comparator::compare
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @covers \Fleshgrinder\Core\Ordering::isGreater
	 * @covers \Fleshgrinder\Core\Ordering::new
	 * @dataProvider provideIsGreaterThanData
	 */
	public static function testIsGreaterThan(bool $expected, Comparable $comparable, $other) {
		static::assertSame($expected, $comparable->isGreaterThan($other));
	}

	/**
	 * @testdox ::isGreaterThan does not throw an exception on type mismatch
	 * @covers \Fleshgrinder\Core\ComparableTrait::doCompareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::isGreaterThan
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::isGreater
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 */
	public static function testIsGreaterThanTypeMismatch() {
		static::assertFalse((new ComparableFake)->isGreaterThan(42));
	}

	/**
	 * @testdox ::isGreaterThan catches exceptions and always returns a boolean
	 * @covers \Fleshgrinder\Core\ComparableTrait::doCompareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::isGreaterThan
	 * @covers \Fleshgrinder\Core\Comparators\ArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\Comparator::compare
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::isGreater
	 * @covers \Fleshgrinder\Core\NullOrdering::new
	 * @covers \Fleshgrinder\Core\UncomparableException::fromIncompatibleTypes
	 */
	public static function testIsGreaterThanExceptionCatching() {
		static::assertFalse((new ComparableFake)->isGreaterThan(new ComparableFake(42)));
	}
}
