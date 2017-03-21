<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * This implementation is broken, because it uses the {@see ComparableTrait}
 * but does not implement the {@see Comparable} interface.
 */
final class BrokenComparable { use ComparableTrait; }

/**
 * This implementation is correct since it implements the {@see Comparable}
 * interface and makes use of the {@see ComparableTrait}. It uses the default
 * {@see ComparableTrait::doCompareTo} implementation, which compares all
 * encapsulated properties; in this case `$value` only.
 *
 * Have a look at the various tests in the `tests/ArrayComparators` directory
 * to get a better understanding of the various array comparator
 * implementations that are available.
 */
final class ComparableFake implements Comparable {
	use ComparableTrait;

	public $value;

	public function __construct($value = \null) {
		$this->value = $value;
	}
}

/**
 * Another correct implementation, specifically made for Prophecy compatibility.
 * The `finale` modifier needs to be removed, otherwise Prophecy cannot
 * intercept calls and verify how many times the method was actually called.
 *
 * @see ComparableTraitTest::testGetComparatorLhsCall
 */
class ComparableSpy implements Comparable {
	use ComparableTrait { compareTo as public superCompareTo; }
	/* @noinspection PhpInconsistentReturnPointsInspection */
	public function compareTo($other): Ordering { }
}

final class ComparableTraitTest extends TestCase {
	/**
	 * @testdox ::getComparator throws an \AssertionError if using class does not implement the \Fleshgrinder\Core\Comparable interface
	 * @covers \Fleshgrinder\Core\ComparableTrait::getComparator
	 * @expectedException \AssertionError
	 */
	public static function testGetComparatorAssertionError() {
		BrokenComparable::getComparator();
	}

	/**
	 * @testdox ::getComparator's comparator throws an \Fleshgrinder\Core\UncomparableException if the left-hand side is not an instance of itself
	 * @covers \Fleshgrinder\Core\ComparableTrait::getComparator
	 * @uses \Fleshgrinder\Core\Comparators\ComparatorDelegate
	 * @uses \Fleshgrinder\Core\UncomparableException
	 * @expectedException \Fleshgrinder\Core\UncomparableException
	 * @expectedExceptionMessage Cannot compare Fleshgrinder\Core\ComparableFake with null
	 */
	public static function testGetComparatorUncomparableException() {
		ComparableFake::getComparator()(\null, \null);
	}

	/**
	 * @testdox ::getComparator's comparator calls ::compareTo of left-hand side argument exactly once if it is an instance of itself
	 * @covers \Fleshgrinder\Core\ComparableTrait::getComparator
	 * @uses \Fleshgrinder\Core\Comparators\ComparatorDelegate
	 * @uses \Fleshgrinder\Core\Ordering
	 */
	public function testGetComparatorLhsCall() {
		/** @var ComparableSpy|ObjectProphecy $spy */
		$spy  = $this->prophesize(ComparableSpy::CLASS);
		$fake = new ComparableSpy;
		/** @var \Prophecy\Prophecy\MethodProphecy $compareTo */
		$compareTo = $spy->compareTo($fake);
		$compareTo->willReturn(Ordering::Equal());

		ComparableSpy::getComparator()($spy->reveal(), $fake);

		$compareTo->shouldHaveBeenCalledTimes(1);
	}

	/**
	 * @testdox user sorting with ::getComparator
	 * @covers \Fleshgrinder\Core\ComparableTrait::compareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::doCompareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::getComparator
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__construct
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::compare
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::doCompare
	 * @covers \Fleshgrinder\Core\Comparators\ComparatorDelegate::__construct
	 * @covers \Fleshgrinder\Core\Comparators\ComparatorDelegate::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\Ordering::__construct
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
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__construct
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::compare
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::doCompare
	 * @covers \Fleshgrinder\Core\Comparators\ComparatorDelegate::__construct
	 * @covers \Fleshgrinder\Core\Comparators\ComparatorDelegate::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\ReverseComparator::__construct
	 * @covers \Fleshgrinder\Core\Comparators\ReverseComparator::__invoke
	 * @covers \Fleshgrinder\Core\Ordering::__construct
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
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__construct
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::compare
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::doCompare
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @covers \Fleshgrinder\Core\Ordering::isLess
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
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 */
	public static function testIsLessThanTypeMismatch() {
		static::assertFalse((new ComparableFake)->isLessThan(42));
	}

	/**
	 * @testdox ::isLessThan catches exceptions and always returns a boolean
	 * @covers \Fleshgrinder\Core\ComparableTrait::doCompareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::isLessThan
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__construct
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::compare
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::doCompare
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::isLess
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @uses \Fleshgrinder\Core\UncomparableException
	 */
	public static function testIsLessThanExceptionCatching() {
		static::assertFalse((new ComparableFake('foo'))->isLessThan(new ComparableFake(42)));
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
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__construct
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::compare
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::doCompare
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @covers \Fleshgrinder\Core\Ordering::isLessOrEqual
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
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 */
	public static function testIsLessThanOrEqualsTypeMismatch() {
		static::assertFalse((new ComparableFake)->isLessThanOrEquals(42));
	}

	/**
	 * @testdox ::isLessThanOrEquals catches exceptions and always returns a boolean
	 * @covers \Fleshgrinder\Core\ComparableTrait::doCompareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::isLessThanOrEquals
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__construct
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::compare
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::doCompare
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::isLessOrEqual
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @uses \Fleshgrinder\Core\UncomparableException
	 */
	public static function testIsLessThanOrEqualsExceptionCatching() {
		static::assertFalse((new ComparableFake('foo'))->isLessThanOrEquals(new ComparableFake(42)));
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
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__construct
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::compare
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::doCompare
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @covers \Fleshgrinder\Core\Ordering::isEqual
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
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 */
	public static function testEqualsTypeMismatch() {
		static::assertFalse((new ComparableFake)->equals(42));
	}

	/**
	 * @testdox ::equals catches exceptions and always returns a boolean
	 * @covers \Fleshgrinder\Core\ComparableTrait::doCompareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::equals
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__construct
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::compare
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::doCompare
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::isEqual
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @uses \Fleshgrinder\Core\UncomparableException
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
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__construct
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::compare
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::doCompare
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @covers \Fleshgrinder\Core\Ordering::isGreaterOrEqual
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
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 */
	public static function testIsGreaterThanOrEqualsTypeMismatch() {
		static::assertFalse((new ComparableFake)->isGreaterThanOrEquals(42));
	}

	/**
	 * @testdox ::isGreaterThanOrEquals catches exceptions and always returns a boolean
	 * @covers \Fleshgrinder\Core\ComparableTrait::doCompareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::isGreaterThanOrEquals
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__construct
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::compare
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::doCompare
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::isGreaterOrEqual
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @uses \Fleshgrinder\Core\UncomparableException
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
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__construct
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::compare
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::doCompare
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @covers \Fleshgrinder\Core\Ordering::isGreater
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
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 */
	public static function testIsGreaterThanTypeMismatch() {
		static::assertFalse((new ComparableFake)->isGreaterThan(42));
	}

	/**
	 * @testdox ::isGreaterThan catches exceptions and always returns a boolean
	 * @covers \Fleshgrinder\Core\ComparableTrait::doCompareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::isGreaterThan
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__construct
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::compare
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::doCompare
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\NullOrdering::__construct
	 * @covers \Fleshgrinder\Core\NullOrdering::isGreater
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @uses \Fleshgrinder\Core\UncomparableException
	 */
	public static function testIsGreaterThanExceptionCatching() {
		static::assertFalse((new ComparableFake)->isGreaterThan(new ComparableFake(42)));
	}
}
