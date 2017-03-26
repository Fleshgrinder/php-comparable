<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\Comparators;

use Fleshgrinder\Core\Ordering;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Fleshgrinder\Core\Comparators\NullableComparator::__invoke
 * @covers \Fleshgrinder\Core\Comparators\NullableComparator::compare
 */
final class NullableComparatorTest extends TestCase {
	public static function provideComparableData() {
		return [
			'null <=> null = EQ'  => [Ordering::Equal(), \null, \null],
			'null <=> mixed = LT' => [Ordering::Less(), \null, 'mixed'],
			'mixed <=> null = GT' => [Ordering::Greater(), 'mixed', \null],
		];
	}

	/**
	 * @dataProvider provideComparableData
	 * @uses \Fleshgrinder\Core\Ordering
	 */
	public static function testInvoke(Ordering $expected, $lhs, $rhs) {
		static::assertEquals($expected, NullableComparator::compare($lhs, $rhs));
	}
}
