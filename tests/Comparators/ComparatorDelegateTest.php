<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\Comparators;

use PHPUnit\Framework\TestCase;

/** @covers \Fleshgrinder\Core\Comparators\ComparatorDelegate::new */
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
	 * @dataProvider provideDelegates
	 * @expectedException \AssertionError
	 * @expectedExceptionMessage Delegate signature is invalid, see Fleshgrinder\Core\Comparators\ComparatorDelegate::__invoke() for required signature
	 */
	public static function testNewAssertionError(callable $delegate) {
		ComparatorDelegate::new($delegate);
	}

	/**
	 * @testdox catches TypeErrors and transforms them into Fleshgrinder\Core\UncomparableExceptions
	 * @covers \Fleshgrinder\Core\Comparators\ComparatorDelegate::__invoke
	 * @covers \Fleshgrinder\Core\UncomparableException::new
	 * @expectedException \Fleshgrinder\Core\UncomparableException
	 * @expectedExceptionMessage Expected DateTime but got null on left- and integer on right-hand side
	 */
	public static function testTypeErrorClassHandling() {
		ComparatorDelegate::new(static function (\DateTime $lhs, \DateTime $rhs): int {
			throw new \BadMethodCallException;
		})(\null, 42);
	}

	/**
	 * @testdox catches TypeErrors and transforms them into Fleshgrinder\Core\UncomparableExceptions
	 * @covers \Fleshgrinder\Core\Comparators\ComparatorDelegate::__invoke
	 * @covers \Fleshgrinder\Core\UncomparableException::new
	 * @expectedException \Fleshgrinder\Core\UncomparableException
	 * @expectedExceptionMessage Expected int but got null on left- and integer on right-hand side
	 */
	public static function testTypeErrorTypeHandling() {
		ComparatorDelegate::new(static function (int $lhs, int $rhs): int {
			throw new \BadMethodCallException;
		})(\null, 42);
	}

	public static function throwTypeError() {
		throw new \TypeError('test');
	}

	public static function provideTypeErrorRethrowingData() {
		return [
			[static function ($lhs, $rhs): int {
				throw new \TypeError('test');
			}],
			[static function ($lhs, $rhs): int {
				static::throwTypeError();
			}],
		];
	}

	/**
	 * @testdox does not catch unrelated TypeErrors and simply rethrows them
	 * @covers \Fleshgrinder\Core\Comparators\ComparatorDelegate::__invoke
	 * @dataProvider provideTypeErrorRethrowingData
	 * @expectedException \TypeError
	 * @expectedExceptionMessage test
	 */
	public static function testTypeErrorRethrowing(callable $delegate) {
		ComparatorDelegate::new($delegate)(\null, \null);
	}
}
