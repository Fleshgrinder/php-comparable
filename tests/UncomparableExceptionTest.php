<?php

namespace Fleshgrinder\Core;

use PHPUnit\Framework\TestCase;

final class UncomparableExceptionTest extends TestCase {

	/**
	 * @covers \Fleshgrinder\Core\Comparison\UncomparableException::fromUnexpectedType
	 */
	public static function testFormatting() {
		$c = ComparableFake::class;
		$e = UncomparableException::fromUnexpectedType($c, null);

		static::assertSame("Cannot compare null with {$c}", $e->getMessage());
	}

}
