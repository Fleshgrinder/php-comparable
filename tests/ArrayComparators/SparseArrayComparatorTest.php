<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\ArrayComparators;

use Fleshgrinder\Core\Ordering;
use PHPUnit\Framework\TestCase;

final class SparseArrayComparatorTest extends TestCase {
	public static function provideComparableData() {
		return [
			'[0 => 1] <=> [1 => 1] === GT'   => [Ordering::Greater(), [0 => 1], [1 => 1]],
			'[1 => 1] <=> [0 => 1] === GT'   => [Ordering::Greater(), [1 => 1], [0 => 1]],
			'[1, 2, 3] <=> [1, 2, 3] === EQ' => [Ordering::Equal(), [1, 2, 3], [1, 2, 3]],
			'[1, 2, 3] <=> [1, 2] === GT'    => [Ordering::Greater(), [1, 2, 3], [1, 2]],
			'[1, 2] <=> [1, 2, 3] === LT'    => [Ordering::Less(), [1, 2], [1, 2, 3]],
		];
	}

	/**
	 * @testdox correctly handles sparse data
	 * @covers \Fleshgrinder\Core\ArrayComparators\SparseArrayComparator::__construct
	 * @covers \Fleshgrinder\Core\ArrayComparators\SparseArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\ArrayComparators\SparseArrayComparator::compare
	 * @covers \Fleshgrinder\Core\ArrayComparators\SparseArrayComparator::doCompare
	 * @covers \Fleshgrinder\Core\ArrayComparators\SparseArrayComparator::handleMissingKey
	 * @covers \Fleshgrinder\Core\ArrayComparators\SparseArrayComparator::handleSizeMismatch
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::new
	 * @dataProvider provideComparableData
	 * @uses \Fleshgrinder\Core\Ordering
	 */
	public static function testCompare(Ordering $expected, array $lhs, array $rhs) {
		static::assertEquals($expected, SparseArrayComparator::compare($lhs, $rhs));
	}
}
