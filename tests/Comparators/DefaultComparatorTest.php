<?php

declare(strict_types = 1);

namespace Fleshgrinder\Core\Comparators;

use Fleshgrinder\Core\Ordering;
use PHPUnit\Framework\TestCase;

final class DefaultComparatorTest extends TestCase {
	/**
	 * @testdox throws an UncomparableException if instances mismatch
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @uses   \Fleshgrinder\Core\UncomparableException
	 * @expectedException \Fleshgrinder\Core\UncomparableException
	 * @expectedExceptionMessage Cannot compare DateTime with stdClass
	 */
	public static function testInvokeInstanceCheck() {
		(new DefaultComparator)(new \DateTime, (object) []);
	}

	/**
	 * @testdox throws an UncomparableException if types mismatch
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @uses   \Fleshgrinder\Core\UncomparableException
	 * @expectedException \Fleshgrinder\Core\UncomparableException
	 * @expectedExceptionMessage Cannot compare integer with float
	 */
	public static function testInvokeTypeCheck() {
		(new DefaultComparator)(1, 1.1);
	}

	/**
	 * @testdox calls compareTo on left-hand side if instances of Comparable are given
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @uses \Fleshgrinder\Core\ComparableTrait
	 * @uses \Fleshgrinder\Core\Ordering
	 */
	public static function testInvokeCompareToCall() {
		static::assertSame(
			Ordering::LT,
			(new DefaultComparator)(Ordering::Less(), Ordering::Greater())
		);
	}

	/**
	 * @testdox uses PHP 7 spaceship operator for non-Comparable values
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 */
	public static function testInvoke() {
		static::assertSame(
			Ordering::LT,
			(new DefaultComparator)(new \DateTime('@0'), new \DateTime)
		);
	}
}
