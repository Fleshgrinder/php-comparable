<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\ArrayComparators\Traits;

use Fleshgrinder\Core\Ordering;
use PHPUnit\Framework\TestCase;

final class ArrayComparatorFake {
	use ArrayComparatorTrait;

	protected static function handleSizeMismatch(int $l_len, int $r_len, int $order): int {
		throw new \BadMethodCallException;
	}

	protected static function handleMissingKey($l_val, $key): int {
		throw new \BadMethodCallException;
	}
}

/**
 * @covers \Fleshgrinder\Core\ArrayComparators\Traits\ArrayComparatorTrait::__invoke
 */
final class ArrayComparatorTraitTest extends TestCase {
	/**
	 * @testdox directly returns with EQ if two empty arrays are given
	 */
	public static function testEmptyArrayShortcut() {
		static::assertSame(Ordering::EQ, (new ArrayComparatorFake)([], []));
	}

	public static function provideMismatchingTypes() {
		return [
			'(null, null)'  => [\null, \null],
			'(array, null)' => [[], \null],
			'(null, array)' => [\null, []],
		];
	}

	/**
	 * @testdox throws a Fleshgrinder\Core\UncomparableException if one of the two arguments is not an array:
	 * @dataProvider provideMismatchingTypes
	 * @expectedException \Fleshgrinder\Core\UncomparableException
	 * @uses \Fleshgrinder\Core\UncomparableException
	 */
	public static function testTypeMismatchException($lhs, $rhs) {
		(new ArrayComparatorFake)($lhs, $rhs);
	}
}
