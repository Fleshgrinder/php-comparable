<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\Comparators;

use Fleshgrinder\Core\Ordering;
use Fleshgrinder\Core\Value;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::__invoke
 */
final class DefaultComparatorTest extends TestCase {
	/**
	 * @testdox throws a Fleshgrinder\Core\UncomparableException if instances mismatch
	 * @expectedException \Fleshgrinder\Core\UncomparableException
	 * @expectedExceptionMessage Cannot compare DateTime with stdClass
	 * @uses \Fleshgrinder\Core\UncomparableException
	 */
	public static function testInvokeInstanceCheck() {
		(new DefaultComparator)(new \DateTime, (object) []);
	}

	public static function provideMismatchingTypes() {
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
					$data["{$l_name} <=> {$r_name}"] = [$l_type, $r_type];
				}
			}
		}

		return $data;
	}

	/**
	 * @testdox throws a Fleshgrinder\Core\UncomparableException if types mismatch:
	 * @dataProvider provideMismatchingTypes
	 * @expectedException \Fleshgrinder\Core\UncomparableException
	 * @uses \Fleshgrinder\Core\UncomparableException
	 */
	public static function testInvokeTypeCheck($lhs, $rhs) {
		(new DefaultComparator)($lhs, $rhs);
	}

	/**
	 * @testdox calls ::compareTo on left-hand side if instances of Fleshgrinder\Core\Comparable is given
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
	 * @testdox uses PHP 7 spaceship operator for instances that do not implement Fleshgrinder\Core\Comparable
	 */
	public static function testInvoke() {
		static::assertSame(
			Ordering::LT,
			(new DefaultComparator)(new \DateTime('@0'), new \DateTime)
		);
	}
}
