<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\ArrayComparators;

use Fleshgrinder\Core\{DataTypeProviderTrait, Ordering};
use PHPUnit\Framework\TestCase;

final class NullableRecursiveArrayComparatorTest extends TestCase {
	use DataTypeProviderTrait;

	public static function provideComparableData() {
		$data = [];

		foreach (static::provideDataTypes() as $type => $value) {
			$data["[{$type}, [[{$type}]]] <=> [{$type}, [[null]]] === GT"] = [Ordering::Greater(), [$value, [[$value]]], [$value, [[\null]]]];
			$data["[{$type}, [[null]]] <=> [{$type}, [[{$type}] === LT"]   = [Ordering::Less(), [$value, [[\null]]], [$value, [[$value]]]];
		}

		return $data;
	}

	/**
	 * @testdox correctly handles sparse data
	 * @covers \Fleshgrinder\Core\ArrayComparators\NullableRecursiveArrayComparator::__construct
	 * @covers \Fleshgrinder\Core\ArrayComparators\NullableRecursiveArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\ArrayComparators\NullableRecursiveArrayComparator::compare
	 * @covers \Fleshgrinder\Core\ArrayComparators\NullableRecursiveArrayComparator::doCompare
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::new
	 * @covers \Fleshgrinder\Core\Comparators\NullableComparator::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\NullableComparator::new
	 * @dataProvider provideComparableData
	 * @uses \Fleshgrinder\Core\Ordering
	 */
	public static function testCompare(Ordering $expected, array $lhs, array $rhs) {
		static::assertEquals($expected, NullableRecursiveArrayComparator::compare($lhs, $rhs));
	}
}
