<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\Comparators;

use Fleshgrinder\Core\{DataTypeProviderTrait, Ordering};
use PHPUnit\Framework\TestCase;

/**
 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::compare
 */
final class DefaultComparatorTest extends TestCase {
	use DataTypeProviderTrait;

	/**
	 * @testdox throws a Fleshgrinder\Core\UncomparableException if instances mismatch
	 * @expectedException \Fleshgrinder\Core\UncomparableException
	 * @expectedExceptionMessage Cannot compare DateTime with stdClass
	 * @uses \Fleshgrinder\Core\UncomparableException
	 */
	public static function testInvokeInstanceCheck() {
		DefaultComparator::compare(new \DateTime, (object) []);
	}

	/**
	 * @testdox throws a Fleshgrinder\Core\UncomparableException if types mismatch:
	 * @dataProvider provideMismatchingTypes
	 * @expectedException \Fleshgrinder\Core\UncomparableException
	 * @uses \Fleshgrinder\Core\UncomparableException
	 */
	public static function testInvokeTypeCheck($lhs, $rhs) {
		DefaultComparator::compare($lhs, $rhs);
	}

	/**
	 * @testdox calls ::compareTo on left-hand side if instances of Fleshgrinder\Core\Comparable is given
	 * @uses \Fleshgrinder\Core\ComparableTrait
	 * @uses \Fleshgrinder\Core\Ordering
	 */
	public static function testInvokeCompareToCall() {
		static::assertEquals(
			Ordering::Less(),
			DefaultComparator::compare(Ordering::Less(), Ordering::Greater())
		);
	}

	/**
	 * @testdox uses PHP 7 spaceship operator for instances that do not implement Fleshgrinder\Core\Comparable
	 * @uses \Fleshgrinder\Core\Ordering
	 */
	public static function testInvoke() {
		static::assertEquals(
			Ordering::Less(),
			DefaultComparator::compare(new \DateTime('@0'), new \DateTime)
		);
	}
}
