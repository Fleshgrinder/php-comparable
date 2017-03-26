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

class ComparableFake implements Comparable {
	use ComparableTrait;

	public $value;

	public function __construct(int $value = 0) {
		$this->value = $value;
	}
}

final class ComparableTraitTest extends TestCase {
	/**
	 * @testdox ::getComparator throws an \AssertionError if using class does not implement the \Fleshgrinder\Core\Comparable interface
	 * @covers \Fleshgrinder\Core\ComparableTrait::getComparator
	 * @expectedException \AssertionError
	 */
	public static function testGetComparatorAssertionError() {
		$broken_comparable = new class { use ComparableTrait; };

		$broken_comparable::getComparator();
	}

	/**
	 * @testdox ::getComparator's comparator throws an \Fleshgrinder\Core\UncomparableException if the left-hand side is not an instance of itself
	 * @covers \Fleshgrinder\Core\ComparableTrait::getComparator
	 * @covers \Fleshgrinder\Core\Comparators\ComparatorDelegate::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\ComparatorDelegate::new
	 * @covers \Fleshgrinder\Core\UncomparableException::new
	 * @expectedException \Fleshgrinder\Core\UncomparableException
	 * @expectedExceptionMessage Expected Fleshgrinder\Core\ComparableFake but got null on left-hand side
	 */
	public static function testGetComparatorUncomparableException() {
		ComparableFake::getComparator()(\null, \null);
	}

	/**
	 * @testdox ::getComparator's comparator calls ::compareTo of left-hand side argument exactly once if it is an instance of itself
	 * @covers \Fleshgrinder\Core\ComparableTrait::getComparator
	 * @covers \Fleshgrinder\Core\Comparators\ComparatorDelegate::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\ComparatorDelegate::new
	 * @uses \Fleshgrinder\Core\Ordering
	 */
	public function testGetComparatorLhsCall() {
		/** @var ComparableFake|ObjectProphecy $spy */
		$spy  = $this->prophesize(ComparableFake::CLASS);
		$fake = new ComparableFake;
		/** @var \Prophecy\Prophecy\MethodProphecy $compareTo */
		$compareTo = $spy->compareTo($fake);
		$compareTo->willReturn(Ordering::Equal());

		ComparableFake::getComparator()($spy->reveal(), $fake);

		$compareTo->shouldHaveBeenCalledTimes(1);
	}

	/**
	 * @testdox user sorting with ::getComparator
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__construct
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::compare
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::doCompare
	 * @covers \Fleshgrinder\Core\ComparableTrait::compareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::compareTypeSafeTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::getComparator
	 * @covers \Fleshgrinder\Core\Comparators\ComparatorDelegate::__invoke
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
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__construct
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::compare
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::doCompare
	 * @covers \Fleshgrinder\Core\ComparableTrait::compareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::compareTypeSafeTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::getComparator
	 * @covers \Fleshgrinder\Core\ComparableTrait::getReverseComparator
	 * @covers \Fleshgrinder\Core\Comparators\ComparatorDelegate::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\ComparatorDelegate::new
	 * @covers \Fleshgrinder\Core\Comparators\ReverseComparator::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\ReverseComparator::new
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
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
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__construct
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::compare
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::doCompare
	 * @covers \Fleshgrinder\Core\ComparableTrait::compareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::compareTypeSafeTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::isLessThan
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
	 * @covers \Fleshgrinder\Core\ComparableTrait::compareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::isLessThan
	 * @uses \Fleshgrinder\Core\UncomparableException
	 */
	public static function testIsLessThanTypeMismatch() {
		static::assertFalse((new ComparableFake)->isLessThan(42));
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
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__construct
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::compare
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::doCompare
	 * @covers \Fleshgrinder\Core\ComparableTrait::compareTypeSafeTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::compareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::isLessThanOrEquals
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::new
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
	 * @covers \Fleshgrinder\Core\ComparableTrait::compareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::isLessThanOrEquals
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @uses \Fleshgrinder\Core\UncomparableException
	 */
	public static function testIsLessThanOrEqualsTypeMismatch() {
		static::assertFalse((new ComparableFake)->isLessThanOrEquals(42));
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
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__construct
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::compare
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::doCompare
	 * @covers \Fleshgrinder\Core\ComparableTrait::compareTypeSafeTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::compareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::equals
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::new
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
	 * @covers \Fleshgrinder\Core\ComparableTrait::compareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::equals
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @uses \Fleshgrinder\Core\UncomparableException
	 */
	public static function testEqualsTypeMismatch() {
		static::assertFalse((new ComparableFake)->equals(42));
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
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__construct
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::compare
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::doCompare
	 * @covers \Fleshgrinder\Core\ComparableTrait::compareTypeSafeTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::compareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::isGreaterThanOrEquals
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::new
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
	 * @covers \Fleshgrinder\Core\ComparableTrait::compareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::isGreaterThanOrEquals
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @covers \Fleshgrinder\Core\UncomparableException::fromIncompatibleTypes
	 * @covers \Fleshgrinder\Core\UncomparableException::new
	 */
	public static function testIsGreaterThanOrEqualsTypeMismatch() {
		static::assertFalse((new ComparableFake)->isGreaterThanOrEquals(42));
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
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__construct
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::compare
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::doCompare
	 * @covers \Fleshgrinder\Core\ComparableTrait::compareTypeSafeTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::compareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::isGreaterThan
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::new
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
	 * @covers \Fleshgrinder\Core\ComparableTrait::compareTo
	 * @covers \Fleshgrinder\Core\ComparableTrait::isGreaterThan
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @covers \Fleshgrinder\Core\UncomparableException::fromIncompatibleTypes
	 * @covers \Fleshgrinder\Core\UncomparableException::new
	 */
	public static function testIsGreaterThanTypeMismatch() {
		static::assertFalse((new ComparableFake)->isGreaterThan(42));
	}
}
