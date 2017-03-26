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

final class RecursiveArrayComparatorTest extends TestCase {
	use DataTypeProviderTrait;

	public static function provideComparableData() {
		$data = [];

		foreach (static::provideDataTypes() as $type => $value) {
			$data["[[{$type}, [{$type}]]] <=> [[{$type}, [{$type}]]] === EQ"] = [Ordering::Equal(), [$value, [$value]], [$value, [$value]]];
		}

		$data['[[Ordering::Less] <=> [Ordering::Greater] === LT'] = [Ordering::Less(), [[Ordering::Less()]], [[Ordering::Greater()]]];
		$data['[[Ordering::Greater] <=> [Ordering::Less] === GT'] = [Ordering::Greater(), [[Ordering::Greater()]], [[Ordering::Less()]]];

		$r1 = \fopen('php://memory', 'rb');
		$r2 = \fopen('php://memory', 'rb');

		$data['[r1, [r2, [r1]]] <=> [r1, [r2, [r2]]] === LT'] = [Ordering::Less(), [$r1, [$r2, [$r1]]], [$r1, [$r2, [$r2]]]];
		$data['[r1, [r2, [r2]]] <=> [r1, [r2, [r1]]] === GT'] = [Ordering::Greater(), [$r1, [$r2, [$r2]]], [$r1, [$r2, [$r1]]]];

		return $data;
	}

	/**
	 * @testdox correctly compares
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__construct
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::compare
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::doCompare
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @covers \Fleshgrinder\Core\Ordering::compareTo
	 * @covers \Fleshgrinder\Core\Ordering::compareTypeSafeTo
	 * @covers \Fleshgrinder\Core\Ordering::new
	 * @covers \Fleshgrinder\Core\Ordering::toInt
	 * @dataProvider provideComparableData
	 */
	public static function testCompareSuccess(Ordering $expected, array $lhs, array $rhs) {
		static::assertEquals($expected, RecursiveArrayComparator::compare($lhs, $rhs));
	}

	public static function provideUncomparableData() {
		$data = static::provideMismatchingTypes();

		foreach ($data as &$value) {
			$value = [[[$value[0], [[$value[0]]]]], [[$value[0], [[$value[1]]]]]];
		}

		return $data;
	}

	/**
	 * @testdox throws a \Fleshgrinder\Core\UncomparableException for
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__construct
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::compare
	 * @covers \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator::doCompare
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::new
	 * @dataProvider provideUncomparableData
	 * @expectedException \Fleshgrinder\Core\UncomparableException
	 * @uses \Fleshgrinder\Core\Ordering
	 * @uses \Fleshgrinder\Core\UncomparableException
	 */
	public static function testCompareFailure(array $lhs, array $rhs) {
		RecursiveArrayComparator::compare($lhs, $rhs);
	}
}
