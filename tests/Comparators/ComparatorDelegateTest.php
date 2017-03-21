<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\Comparators;

use PHPUnit\Framework\TestCase;

final class ComparatorDelegateTest extends TestCase {
	public static function provideDelegates() {
		return [
			'zero params'       => [function (): int { }],
			'one param'         => [function ($param): int { }],
			'three params'      => [function ($p1, $p2, $p3): int { }],
			'no return type'    => [function ($lhs, $rhs) { }],
			'wrong return type' => [function ($lhs, $rhs): bool { }],
		];
	}

	/**
	 * @testdox throws AssertionError if delegate signature does not match required signature
	 * @covers \Fleshgrinder\Core\Comparators\ComparatorDelegate::__construct
	 * @dataProvider provideDelegates
	 * @expectedException \AssertionError
	 * @expectedExceptionMessage Delegate signature is invalid, see Fleshgrinder\Core\Comparators\ComparatorDelegate::__invoke() for required signature
	 */
	public static function testNewAssertionError(callable $delegate) {
		new ComparatorDelegate($delegate);
	}
}
