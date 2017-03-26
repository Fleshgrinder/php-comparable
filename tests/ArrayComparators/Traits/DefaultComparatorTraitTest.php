<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\ArrayComparators\Traits;

use Fleshgrinder\Core\Comparators\DefaultComparator;
use PHPUnit\Framework\TestCase;

final class DefaultComparatorFake {
	use ArrayComparatorTrait, DefaultComparatorTrait;

	protected static function handleSizeMismatch(int $l_len, int $r_len, int $order): int {
		throw new \BadMethodCallException;
	}

	protected static function handleMissingKey($l_val, $key): int {
		throw new \BadMethodCallException;
	}
}

final class DefaultComparatorTraitTest extends TestCase {
	/**
	 * @testdox instantiates and exports a \Fleshgrinder\Core\Comparators\DefaultComparator instance
	 * @covers \Fleshgrinder\Core\ArrayComparators\Traits\DefaultComparatorTrait::__construct
	 * @covers \Fleshgrinder\Core\Comparators\DefaultComparator::new
	 */
	public static function testConstruct() {
		static::assertAttributeInstanceOf(DefaultComparator::CLASS, 'comparator', DefaultComparatorFake::new());
	}
}
