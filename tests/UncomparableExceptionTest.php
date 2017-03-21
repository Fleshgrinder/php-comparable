<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core;

use PHPUnit\Framework\TestCase;

final class UncomparableExceptionTest extends TestCase {
	/**
	 * @testdox ::fromIncompatibleTypes sets format arguments correctly
	 * @covers \Fleshgrinder\Core\UncomparableException::fromIncompatibleTypes
	 */
	public static function testFromIncompatibleTypes() {
		$expected = 'Cannot compare string with integer';
		$actual   = UncomparableException::fromIncompatibleTypes('str', 123);

		static::assertSame($expected, $actual->getMessage());
	}

	/**
	 * @testdox ::fromUnexpectedType sets format arguments correctly
	 * @covers \Fleshgrinder\Core\UncomparableException::fromUnexpectedType
	 */
	public static function testFromUnexpectedType() {
		$expected = 'Cannot compare foo with array';
		$actual   = UncomparableException::fromUnexpectedType('foo', []);

		static::assertSame($expected, $actual->getMessage());
	}
}
