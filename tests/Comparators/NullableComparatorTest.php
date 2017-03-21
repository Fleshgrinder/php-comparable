<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\Comparators;

use Fleshgrinder\Core\Ordering;
use PHPUnit\Framework\TestCase;

final class NullableComparatorTest extends TestCase {
	public static function provide() {
		return [
			'null <=> null = EQ'  => [Ordering::EQ, \null, \null],
			'null <=> mixed = LT' => [Ordering::LT, \null, 'mixed'],
			'mixed <=> null = GT' => [Ordering::GT, 'mixed', \null],
		];
	}

	/**
	 * @covers \Fleshgrinder\Core\Comparators\NullableComparator::__invoke
	 * @dataProvider provide
	 */
	public static function testInvoke(int $expected, $lhs, $rhs) {
		static::assertSame($expected, (new NullableComparator)($lhs, $rhs));
	}
}
