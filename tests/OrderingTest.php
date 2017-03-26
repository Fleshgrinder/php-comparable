<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core;

use PHPUnit\Framework\TestCase;

/** @covers \Fleshgrinder\Core\Ordering::__construct */
final class OrderingTest extends TestCase {
	use DataTypeProviderTrait;

	/**
	 * @testdox ::Less is -1
	 * @covers \Fleshgrinder\Core\Ordering::Less
	 */
	public static function testLess() {
		static::assertAttributeSame(-1, 'order', Ordering::Less());
	}

	/**
	 * @testdox ::Equal is 0
	 * @covers \Fleshgrinder\Core\Ordering::Equal
	 */
	public static function testEqual() {
		static::assertAttributeSame(0, 'order', Ordering::Equal());
	}

	/**
	 * @testdox ::Greater is 1
	 * @covers \Fleshgrinder\Core\Ordering::Greater
	 */
	public static function testGreater() {
		static::assertAttributeSame(1, 'order', Ordering::Greater());
	}

	public static function provideIsMethodData() {
		return [
			// @formatter:off
			'less'    => [ Ordering::Less(),    \true,  \true,  \false, \false, \false ],
			'equal'   => [ Ordering::Equal(),   \false, \true,  \true,  \true,  \false ],
			'greater' => [ Ordering::Greater(), \false, \false, \false, \true,  \true  ],
		    // @formatter:on
		];
	}

	/**
	 * @testdox is-method results for
	 * @covers \Fleshgrinder\Core\Ordering::isLess
	 * @covers \Fleshgrinder\Core\Ordering::isLessOrEqual
	 * @covers \Fleshgrinder\Core\Ordering::isEqual
	 * @covers \Fleshgrinder\Core\Ordering::isGreaterOrEqual
	 * @covers \Fleshgrinder\Core\Ordering::isGreater
	 * @dataProvider provideIsMethodData
	 */
	public static function testIsMethods(Ordering $ordering, bool $less, bool $less_or_equal, bool $equal, bool $greater_or_equal, bool $greater) {
		// @formatter:off
		static::assertSame( $less,             $ordering->isLess()           );
		static::assertSame( $less_or_equal,    $ordering->isLessOrEqual()    );
		static::assertSame( $equal,            $ordering->isEqual()          );
		static::assertSame( $greater_or_equal, $ordering->isGreaterOrEqual() );
		static::assertSame( $greater,          $ordering->isGreater()        );
		// @formatter:on
	}

	public static function provideThenData() {
		$lt = Ordering::Less();
		$eq = Ordering::Equal();
		$gt = Ordering::Greater();

		return [
			'itself if less'    => [$lt, $lt, $eq],
			'other if equal'    => [$lt, $eq, $lt],
			'itself if greater' => [$gt, $gt, $eq],
		];
	}

	/**
	 * @testdox ::then returns
	 * @covers \Fleshgrinder\Core\Ordering::then
	 * @dataProvider provideThenData
	 */
	public static function testThen(Ordering $expected, Ordering $ordering, Ordering $other) {
		static::assertSame($expected, $ordering->then($other));
	}

	public static function provideThenWithData() {
		$lt = Ordering::Less();
		$eq = Ordering::Equal();
		$gt = Ordering::Greater();

		return [
			'itself if less'           => [$lt, $lt, function () { throw new \BadMethodCallException; }],
			'callback result if equal' => [$gt, $eq, function () use ($gt) { return $gt; }],
			'itself if greater'        => [$gt, $gt, function () { throw new \BadMethodCallException; }],
		];
	}

	/**
	 * @testdox ::thenWith returns
	 * @covers \Fleshgrinder\Core\Ordering::thenWith
	 * @dataProvider provideThenWithData
	 */
	public static function testThenWith(Ordering $expected, Ordering $ordering, callable $f) {
		static::assertSame($expected, $ordering->thenWith($f));
	}

	public static function provideIntData() {
		return [
			'PHP_INT_MIN' => [\PHP_INT_MIN],
			'integer -2'  => [-2],
			'integer -1'  => [-1],
			'integer 0'   => [0],
			'integer 1'   => [1],
			'integer 2'   => [2],
			'PHP_INT_MAX' => [\PHP_INT_MAX],
		];
	}

	/**
	 * @testdox integer value is always in [-1, 1] even if created with
	 * @covers \Fleshgrinder\Core\Ordering::new
	 * @covers \Fleshgrinder\Core\Ordering::toInt
	 * @dataProvider provideIntData
	 */
	public static function testNewAndToInt(int $int) {
		$ord = Ordering::new($int)->toInt();

		static::assertTrue(Ordering::LT <= $ord && $ord <= Ordering::GT);
	}

	public static function provideToReverseData() {
		$lt = Ordering::Less();
		$eq = Ordering::Equal();
		$gt = Ordering::Greater();

		return [
			'turns less into greater'                           => [$gt, $lt],
			'keeps equal as equal (but returns a new instance)' => [$eq, $eq],
			'turns greater into less'                           => [$lt, $gt],
		];
	}

	/**
	 * @testdox ::toReverse
	 * @covers \Fleshgrinder\Core\Ordering::toReverse
	 * @dataProvider provideToReverseData
	 */
	public static function testToReverse(Ordering $expected, Ordering $ordering) {
		$actual = $ordering->toReverse();

		static::assertEquals($expected, $actual);
		static::assertNotSame($actual, $ordering);
	}

	public static function provideComparableData() {
		return [
			// @formatter:off
			'Less == Less'       => [Ordering::Equal(),   Ordering::Less(),    Ordering::Less()],
			'Less < Equal'       => [Ordering::Less(),    Ordering::Less(),    Ordering::Equal()],
			'Less < Greater'     => [Ordering::Less(),    Ordering::Less(),    Ordering::Greater()],

			'Equal > Less'       => [Ordering::Greater(), Ordering::Equal(),   Ordering::Less()],
			'Equal == Equal'     => [Ordering::Equal(),   Ordering::Equal(),   Ordering::Equal()],
			'Equal < Greater'    => [Ordering::Less(),    Ordering::Equal(),   Ordering::Greater()],

			'Greater > Less'     => [Ordering::Greater(), Ordering::Greater(), Ordering::Less()],
			'Greater > Equal'    => [Ordering::Greater(), Ordering::Greater(), Ordering::Equal()],
			'Greater == Greater' => [Ordering::Equal(),   Ordering::Greater(), Ordering::Greater()],
			// @formatter:on
		];
	}

	/**
	 * @testdox is comparable to Ordering where
	 * @covers \Fleshgrinder\Core\Ordering::compareTypeSafeTo
	 * @covers \Fleshgrinder\Core\Ordering::toInt
	 * @uses \Fleshgrinder\Core\ComparableTrait::compareTo
	 * @dataProvider provideComparableData
	 */
	public static function testCompareTo(Ordering $expected, Ordering $ordering, Ordering $other) {
		static::assertEquals($expected, $ordering->compareTo($other));
	}

	public static function provideDeveloperStrings() {
		return [
			Ordering::CLASS . '::Less'    => [Ordering::CLASS . '::Less', Ordering::Less()],
			Ordering::CLASS . '::Equal'   => [Ordering::CLASS . '::Equal', Ordering::Equal()],
			Ordering::CLASS . '::Greater' => [Ordering::CLASS . '::Greater', Ordering::Greater()],
		];
	}

	/**
	 * @testdox ::__toString returns
	 * @covers \Fleshgrinder\Core\Ordering::__toString
	 * @covers \Fleshgrinder\Core\Ordering::toString
	 * @dataProvider provideDeveloperStrings
	 */
	public static function testMagicToString(string $expected, Ordering $ordering) {
		static::assertSame($expected, $ordering->__toString());
	}

	public static function provideUserStrings() {
		return [
			'less'    => ['less', Ordering::Less()],
			'equal'   => ['equal', Ordering::Equal()],
			'greater' => ['greater', Ordering::Greater()],
		];
	}

	/**
	 * @testdox ::toString returns
	 * @covers \Fleshgrinder\Core\Ordering::toString
	 * @dataProvider provideUserStrings
	 */
	public static function testToString(string $expected, Ordering $ordering) {
		static::assertSame($expected, $ordering->toString());
	}
}
