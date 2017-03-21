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

final class SparseArrayComparatorFake {
	use ArrayComparatorTrait, SparseArrayComparatorTrait;

	public function __construct() {
		$this->comparator = function () { };
	}
}

/**
 * @covers \Fleshgrinder\Core\ArrayComparators\Traits\ArrayComparatorTrait::__invoke
 */
final class SparseArrayComparatorTraitTest extends TestCase {
	public static function provideLengthMismatches() {
		return [
			'(0, 1)' => [Ordering::LT, [], [\null]],
			'(1, 0)' => [Ordering::GT, [\null], []],
		];
	}

	/**
	 * @testdox returns the comparison result of the mismatched sizes
	 * @covers \Fleshgrinder\Core\ArrayComparators\Traits\SparseArrayComparatorTrait::handleSizeMismatch
	 * @dataProvider provideLengthMismatches
	 */
	public static function testSizeMismatch(int $expected, array $lhs, array $rhs) {
		static::assertSame($expected, (new SparseArrayComparatorFake)($lhs, $rhs));
	}

	/**
	 * @testdox returns greater if key is missing from right-hand side
	 * @covers \Fleshgrinder\Core\ArrayComparators\Traits\ArrayComparatorTrait::doCompare
	 * @covers \Fleshgrinder\Core\ArrayComparators\Traits\SparseArrayComparatorTrait::handleMissingKey
	 */
	public static function testMissingKey() {
		static::assertSame(Ordering::GT, (new SparseArrayComparatorFake)([0 => 'foo'], [1 => 'foo']));
	}
}
