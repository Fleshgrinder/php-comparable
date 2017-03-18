<?php

declare(strict_types = 1);

namespace Fleshgrinder\Core\Comparators;

use PHPUnit\Framework\TestCase;

final class ReverseComparatorTest extends TestCase {
	/**
	 * @testdox is constructable from a callable
	 * @covers \Fleshgrinder\Core\Comparators\ReverseComparator::__construct
	 * @covers \Fleshgrinder\Core\Comparators\ReverseComparator::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\ReverseComparator::fromCallable
	 * @covers \Fleshgrinder\Core\Comparators\ReverseComparator::new
	 * @uses \Fleshgrinder\Core\Comparators\ComparatorDelegate
	 */
	public static function test() {
		static::assertSame(42, ReverseComparator::fromCallable(function ($lhs, $rhs): int {
			return $lhs + $rhs;
		})(21, 21));
	}
}
