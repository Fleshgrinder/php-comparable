<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\ArrayComparators;

use Fleshgrinder\Core\{
	Ordering, Value
};
use PHPUnit\Framework\TestCase;

final class ArrayComparatorTest extends TestCase {
	public static function provideComparableData() {
		$r1 = \fopen('php://memory', 'rb');
		$r2 = \fopen('php://memory', 'rb');

		return [
			'[[]] <=> [[]] === Ordering::EQ'             => [Ordering::Equal(), [[]], [[]]],
			'[true] <=> [true] === Ordering::EQ'         => [Ordering::Equal(), [\true], [\true]],
			'[false] <=> [false] === Ordering::EQ'       => [Ordering::Equal(), [\false], [\false]],
			'[1.1] <=> [1.1] === Ordering::EQ'           => [Ordering::Equal(), [1.1], [1.1]],
			'[1] <=> [1] === Ordering::EQ'               => [Ordering::Equal(), [1], [1]],
			'[null] <=> [null] === Ordering::EQ'         => [Ordering::Equal(), [\null], [\null]],
			'[object] <=> [object] === Ordering::EQ'     => [Ordering::Equal(), [(object) []], [(object) []]],
			'[resource] <=> [resource] === Ordering::EQ' => [Ordering::Equal(), [$r1], [$r1]],
			'["foo"] <=> ["foo"] === Ordering::EQ'       => [Ordering::Equal(), ['foo'], ['foo']],

			'[[Ordering::Less] <=> [Ordering::Greater] === Ordering::LT' => [Ordering::Less(), [Ordering::Less()], [Ordering::Greater()]],
			'[[Ordering::Greater] <=> [Ordering::Less] === Ordering::GT' => [Ordering::Greater(), [Ordering::Greater()], [Ordering::Less()]],

			'[r1, r2, r1] <=> [r1, r2, r2] === Ordering::LT' => [Ordering::Less(), [$r1, $r2, $r1], [$r1, $r2, $r2]],
			'[r1, r2, r2] <=> [r1, r2, r1] === Ordering::GT' => [Ordering::Greater(), [$r1, $r2, $r2], [$r1, $r2, $r1]],

			// Note that the following would lead to an UncomparableException
			// if the recursive version would be used. The non-recursive
			// implementation uses PHP’s built-in comparison for every nested
			// item, regardless of its type!
			'[[null]] <=> [[0]] === Ordering::EQ' => [Ordering::Equal(), [[\null]], [[0]]],

			// The same is true if the size of nested arrays mismatch…
			'[[1, 1]] <=> [[1]] === Ordering::GT' => [Ordering::Greater(), [[1, 1]], [[1]]],

			// …or keys are missing.
			'[[0 => 1]] <=> [[1 => 1]] === Ordering::GT' => [Ordering::Greater(), [[0 => 1]], [[1 => 1]]],
		];
	}

	/**
	 * @testdox correctly compares
	 * @covers \Fleshgrinder\Core\ArrayComparators\ArrayComparator::__construct
	 * @covers \Fleshgrinder\Core\ArrayComparators\ArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\ArrayComparators\ArrayComparator::compare
	 * @covers \Fleshgrinder\Core\ArrayComparators\ArrayComparator::doCompare
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @covers \Fleshgrinder\Core\Ordering::__construct
	 * @covers \Fleshgrinder\Core\Ordering::compareTo
	 * @covers \Fleshgrinder\Core\Ordering::doCompareTo
	 * @covers \Fleshgrinder\Core\Ordering::toInt
	 * @dataProvider provideComparableData
	 */
	public static function testCompareSuccess(Ordering $expected, array $lhs, array $rhs) {
		static::assertEquals($expected, ArrayComparator::compare($lhs, $rhs));
	}

	public static function provideUncomparableData() {
		$types = [
			Value::TYPE_ARRAY    => [],
			Value::TYPE_BOOL     => \true,
			Value::TYPE_FLOAT    => 1.1,
			Value::TYPE_INT      => 1,
			Value::TYPE_NULL     => \null,
			Value::TYPE_OBJECT   => (object) [],
			Value::TYPE_RESOURCE => \fopen('php://memory', 'rb'),
			Value::TYPE_STRING   => 'string',
		];

		$data = [];
		foreach ($types as $l_name => $l_type) {
			foreach ($types as $r_name => $r_type) {
				if ($l_name !== $r_name) {
					$data["[{$l_name}] <=> [{$r_name}]"] = [[$l_type], [$r_type]];
				}
			}
		}

		return $data;
	}

	/**
	 * @testdox throws a \Fleshgrinder\Core\UncomparableException for
	 * @covers \Fleshgrinder\Core\ArrayComparators\ArrayComparator::__construct
	 * @covers \Fleshgrinder\Core\ArrayComparators\ArrayComparator::__invoke
	 * @covers \Fleshgrinder\Core\ArrayComparators\ArrayComparator::compare
	 * @covers \Fleshgrinder\Core\ArrayComparators\ArrayComparator::doCompare
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
	 * @dataProvider provideUncomparableData
	 * @expectedException \Fleshgrinder\Core\UncomparableException
	 * @uses \Fleshgrinder\Core\Ordering
	 * @uses \Fleshgrinder\Core\UncomparableException
	 */
	public static function testCompareFailure(array $lhs, array $rhs) {
		ArrayComparator::compare($lhs, $rhs);
	}
}
