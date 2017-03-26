<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\ArrayComparators\Traits;

use Fleshgrinder\Core\Comparators\NullableComparator;
use PHPUnit\Framework\TestCase;

final class NullableComparatorFake {
	use ArrayComparatorTrait, NullableComparatorTrait;

	protected static function handleSizeMismatch(int $l_len, int $r_len, int $order): int {
		throw new \BadMethodCallException;
	}

	protected static function handleMissingKey($l_val, $key): int {
		throw new \BadMethodCallException;
	}
}

final class NullableComparatorTraitTest extends TestCase {
	/**
	 * @testdox instantiates and exports a \Fleshgrinder\Core\Comparators\NullableComparator instance
	 * @covers \Fleshgrinder\Core\ArrayComparators\Traits\NullableComparatorTrait::__construct
	 * @covers \Fleshgrinder\Core\Comparators\NullableComparator::new
	 */
	public static function testConstruct() {
		static::assertAttributeInstanceOf(NullableComparator::CLASS, 'comparator', NullableComparatorFake::new());
	}
}
