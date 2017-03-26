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

final class NullableArrayComparatorTest extends TestCase {
	use DataTypeProviderTrait;

	public static function provideComparableData() {
		$data = [];

		foreach (static::provideDataTypes() as $type => $value) {
			$data["[{$type}] <=> [null] === GT"] = [Ordering::Greater(), [$value], [\null]];
			$data["[null] <=> [{$type}] === LT"] = [Ordering::Less(), [\null], [$value]];
		}

		return $data;
	}

	/**
	 * @testdox correctly handles sparse data
	 * @covers \Fleshgrinder\Core\ArrayComparators\NullableArrayComparator::__construct
	 * @covers \Fleshgrinder\Core\ArrayComparators\NullableArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\ArrayComparators\NullableArrayComparator::compare
	 * @covers \Fleshgrinder\Core\ArrayComparators\NullableArrayComparator::doCompare
	 * @covers \Fleshgrinder\Core\Comparators\NullableComparator::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\NullableComparator::new
	 * @dataProvider provideComparableData
	 * @uses \Fleshgrinder\Core\Ordering
	 */
	public static function testCompare(Ordering $expected, array $lhs, array $rhs) {
		static::assertEquals($expected, NullableArrayComparator::compare($lhs, $rhs));
	}
}
