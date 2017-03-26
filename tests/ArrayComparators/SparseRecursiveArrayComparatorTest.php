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

final class SparseRecursiveArrayComparatorTest extends TestCase {
	public static function provideComparableData() {
		return [
			'[1, [2, [0 => 3]]] <=> [1, [2, [1 => 3]]] === GT' => [Ordering::Greater(), [1, [2, [0 => 3]]], [1, [2, [1 => 3]]]],
			'[1, [2, [1 => 3]]] <=> [1, [2, [0 => 3]]] === GT' => [Ordering::Greater(), [1, [2, [1 => 3]]], [1, [2, [0 => 3]]]],
			'[1, [2, [3]]] <=> [1, [2, [3]]] === EQ'           => [Ordering::Equal(), [1, [2, [3]]], [1, [2, [3]]]],
			'[1, [2, [3]]] <=> [1, [2, []] === GT'             => [Ordering::Greater(), [1, [2, [3]]], [1, [2, []]]],
			'[1, [2, []] <=> [1, [2, [3]]] === LT'             => [Ordering::Less(), [1, [2, []]], [1, [2, [3]]]],
		];
	}

	/**
	 * @testdox correctly handles sparse data
	 * @covers \Fleshgrinder\Core\ArrayComparators\SparseRecursiveArrayComparator::__construct
	 * @covers \Fleshgrinder\Core\ArrayComparators\SparseRecursiveArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\ArrayComparators\SparseRecursiveArrayComparator::compare
	 * @covers \Fleshgrinder\Core\ArrayComparators\SparseRecursiveArrayComparator::doCompare
	 * @covers \Fleshgrinder\Core\ArrayComparators\SparseRecursiveArrayComparator::handleMissingKey
	 * @covers \Fleshgrinder\Core\ArrayComparators\SparseRecursiveArrayComparator::handleSizeMismatch
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::new
	 * @dataProvider provideComparableData
	 * @uses \Fleshgrinder\Core\Ordering
	 */
	public static function testCompareSuccess(Ordering $expected, array $lhs, array $rhs) {
		static::assertEquals($expected, SparseRecursiveArrayComparator::compare($lhs, $rhs));
	}
}
