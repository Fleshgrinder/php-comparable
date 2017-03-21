<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\ArrayComparators\Traits;

use PHPUnit\Framework\TestCase;

final class StrictArrayComparatorFake {
	use ArrayComparatorTrait, StrictArrayComparatorTrait;

	public function __construct() {
		$this->comparator = function () {};
	}
}

/**
 * @covers \Fleshgrinder\Core\ArrayComparators\Traits\ArrayComparatorTrait::__invoke
 */
final class StrictArrayComparatorTraitTest extends TestCase {
	public static function provideLengthMismatches() {
		return [
			'(0, 1)' => [[], [\null]],
			'(1, 0)' => [[\null], []],
		];
	}

	/**
	 * @testdox throws a Fleshgrinder\Core\UncomparableException if input array sizes mismatch:
	 * @covers \Fleshgrinder\Core\ArrayComparators\Traits\StrictArrayComparatorTrait::handleSizeMismatch
	 * @dataProvider provideLengthMismatches
	 * @expectedException \Fleshgrinder\Core\UncomparableException
	 * @expectedExceptionMessageRegExp /Cannot compare sparse arrays, got \d elements? on left- and \d elements? on right-hand side/
	 * @uses \Fleshgrinder\Core\UncomparableException
	 */
	public static function testSizeMismatch($lhs, $rhs) {
		(new StrictArrayComparatorFake)($lhs, $rhs);
	}

	/**
	 * @testdox throws a Fleshgrinder\Core\UncomparableException if a key from the left-hand side is missing from the right-hand side
	 * @covers \Fleshgrinder\Core\ArrayComparators\Traits\ArrayComparatorTrait::doCompare
	 * @covers \Fleshgrinder\Core\ArrayComparators\Traits\StrictArrayComparatorTrait::handleMissingKey
	 * @expectedException \Fleshgrinder\Core\UncomparableException
	 * @expectedExceptionMessage Cannot compare string against void, key `0` missing from right-hand side
	 * @uses \Fleshgrinder\Core\UncomparableException
	 */
	public static function testMissingKey() {
		(new StrictArrayComparatorFake)([0 => 'foo'], [1 => 'foo']);
	}
}
