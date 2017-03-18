<?php

declare(strict_types = 1);

namespace Fleshgrinder\Core;

use PHPUnit\Framework\TestCase;

final class UncomparableExceptionTest extends TestCase {
	/**
	 * @testdox ::againstVoid sets format arguments correctly
	 * @covers \Fleshgrinder\Core\UncomparableException::againstVoid
	 */
	public static function testAgainstVoidMessage() {
		static::assertSame(
			'Cannot compare ' . Value::TYPE_NULL . ' against void, because of reasons',
			UncomparableException::againstVoid(\null, ', because of reasons')->getMessage()
		);
	}

	/**
	 * @testdox ::fromIncompatibleTypes sets format arguments correctly
	 * @covers \Fleshgrinder\Core\UncomparableException::fromIncompatibleTypes
	 */
	public static function testFromIncompatibleTypes() {
		static::assertSame(
			'Cannot compare ' . Value::TYPE_STRING . ' with ' . Value::TYPE_INT,
			UncomparableException::fromIncompatibleTypes('str', 123)->getMessage()
		);
	}

	/**
	 * @testdox ::fromUnexpectedType sets format arguments correctly
	 * @covers \Fleshgrinder\Core\UncomparableException::fromUnexpectedType
	 */
	public static function testFromUnexpectedType() {
		static::assertSame(
			'Cannot compare foo with ' . Value::TYPE_ARRAY,
			UncomparableException::fromUnexpectedType('foo', [])->getMessage()
		);
	}

	/**
	 * @testdox ::fromUnexpectedTypes sets format arguments correctly
	 * @covers \Fleshgrinder\Core\UncomparableException::fromUnexpectedTypes
	 */
	public static function testFromUnexpectedTypes() {
		static::assertSame(
			'Can compare foos only, got ' . Value::TYPE_BOOL . ' for left- and ' . Value::TYPE_FLOAT . ' for right-hand side',
			UncomparableException::fromUnexpectedTypes('foo', \true, 1.2)->getMessage()
		);
	}
}
