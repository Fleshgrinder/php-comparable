<?php

namespace Fleshgrinder\Core\Comparison\Comparators;

use Fleshgrinder\Core\Comparison\ComparableFake;
use Fleshgrinder\Core\Comparison\Ordering;
use PHPUnit\Framework\TestCase;

final class ArrayComparatorTest extends TestCase {

	public static function provideCompareData() {
		return [
			'less' => [
				Ordering::Less(),
				[new ComparableFake, -1],
				[new ComparableFake, 0],
			],
			'equal' => [
				Ordering::Equal(),
				[new ComparableFake, 0],
			    [new ComparableFake, 0],
			],
		    'greater' => [
		    	Ordering::Greater(),
		        [new ComparableFake, 1],
		        [new ComparableFake, 0],
		    ],
		];
	}

	/**
	 * @covers \Fleshgrinder\Core\Comparison\Comparators\ArrayRecursiveComparator::compare()
	 * @covers \Fleshgrinder\Core\Comparison\Comparators\ArrayRecursiveComparator::compareWith()
	 * @covers \Fleshgrinder\Core\Comparison\Comparators\ArrayRecursiveComparator::doCompare()
	 * @uses \Fleshgrinder\Core\Comparison\ComparableTrait
	 * @uses \Fleshgrinder\Core\Comparison\Ordering
	 * @dataProvider provideCompareData
	 */
	public static function testCompare(Ordering $expected, array $lhs, array $rhs) {
		static::assertEquals($expected, ArrayRecursiveComparator::compare($lhs, $rhs));
	}

	/**
	 * @covers \Fleshgrinder\Core\Comparison\Comparators\ArrayRecursiveComparator::toClosure()
	 * @covers \Fleshgrinder\Core\Comparison\Comparators\ArrayRecursiveComparator::doCompare()
	 * @uses \Fleshgrinder\Core\Comparison\ComparableTrait
	 * @uses \Fleshgrinder\Core\Comparison\Ordering
	 */
	public static function testClosure() {
		$c1 = new ComparableFake(1);
		$c2 = new ComparableFake(2);
		$c3 = new ComparableFake(3);

		$data = [$c3, $c2, $c1];

		\usort($data, ArrayRecursiveComparator::toClosure());

		static::assertSame([$c1, $c2, $c3], $data);
	}

	/**
	 * @covers \Fleshgrinder\Core\Comparison\Comparators\ArrayRecursiveComparator::toReverseClosure()
	 * @covers \Fleshgrinder\Core\Comparison\Comparators\ArrayRecursiveComparator::doCompare()
	 * @uses \Fleshgrinder\Core\Comparison\ComparableTrait
	 * @uses \Fleshgrinder\Core\Comparison\Ordering
	 */
	public static function testReverseClosure() {
		$c1 = new ComparableFake(1);
		$c2 = new ComparableFake(2);
		$c3 = new ComparableFake(3);

		$data = [$c1, $c2, $c3];

		\usort($data, ArrayRecursiveComparator::toReverseClosure());

		static::assertSame([$c3, $c2, $c1], $data);
	}

}
